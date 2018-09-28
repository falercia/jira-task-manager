<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Common\MethodDefinition;
use Common\RequestMethod;
use App\Models\Task;
use App\Models\TaskWorkLog;
use App\Models\Board;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Mail;

class JiraRequestController extends Controller {

   private $methodDefinition;
   private $token;
   private $returnData;

   public function __construct() {
      $this->methodDefinition = new MethodDefinition();
      $this->returnData = array();
      $this->returnData['boards'] = 0;
      $this->returnData['tasks'] = 0;
      $this->returnData['users'] = 0;
      $this->returnData['worklogs'] = 0;
   }

   private function getCommonMethodData($method, $data = array(), $token = false) {
      $data['url'] = $this->methodDefinition->getMethod($data)[$method]['url'];
      $data['http_verb'] = $this->methodDefinition->getMethod($data)[$method]['http_verb'];

      $token = $token ? $token : session('token', false);
      $token = env('JIRA_TEMP_DEFAULT_TOKEN');

      $data['headers'] = $this->methodDefinition->getHeaders($token);

      return $data;
   }

   public function syncUsers() {
      $data = $this->getCommonMethodData('AllUsers');

      $response = RequestMethod::sendRequest($data);


      if ($response['http_code'] == '200') {
         $users = json_decode($response['response_body'], true);
         foreach ($users as $user) {
            if (strpos($user['emailAddress'], '@hubchain.io') || strpos($user['emailAddress'], '@hubchain.com')) {
               $tempUser = array(
                   'id' => $user['accountId'],
                   'name' => $user['displayName'],
                   'email' => $user['emailAddress'],
                   'jira_key' => $user['key'],
                   'is_resource' => in_array($user['emailAddress'], array('alex.ritchie@hubchain.io', 'admin@hubchain.io', 'rodrigo.pimenta@hubchain.io', 'miguel.cece@hubchain.io', 'paulo.stahl@hubchain.com', 'thais.efland@hubchain.com')) ? 'N' : 'Y',
               );
               User::updateOrCreate(['id' => $tempUser['id']], $tempUser);
               $this->returnData['users'] ++;
            }
         }
      }
   }

   public function syncAllBoards() {
      $data = $this->getCommonMethodData('AllBoards');

      $response = RequestMethod::sendRequest($data);

      if ($response['http_code'] == '200') {
         $boards = json_decode($response['response_body'], true)['values'];
         foreach ($boards as $board) {
            $tempBoard = array(
                'id' => $board['id'],
                'name' => $board['location']['name'],
                'type' => $board['type'],
                'project_id' => $board['location']['projectId'],
            );
            Board::updateOrCreate(['id' => $tempBoard['id']], $tempBoard);
            $this->returnData['boards'] ++;
         }

         return response()->json('OK');
      } else {
         return response()->json($response);
      }
   }

   public function getAllIssuesFromBoard($boardId) {
      $data = $this->getCommonMethodData('AllIssuesFromBoard', array('board_id' => $boardId));

      $response = RequestMethod::sendRequest($data);

      if ($response['http_code'] == '200') {
         $issues = json_decode($response['response_body'], true)['issues'];
         $this->syncIssue($issues, $boardId);
         return true;
      } else {
         return 'Fail: ' . json_encode($response);
      }
   }

   public function getIssue($key) {
      $data = $this->getCommonMethodData('Issue', array('key' => $key));

      $response = RequestMethod::sendRequest($data);

      if ($response['http_code'] == '200') {
         $issue = json_decode($response['response_body'], true);
         $this->syncIssue(array($issue));
         return response()->json('OK!');
      } else {
         return response()->json('Fail');
      }
   }

   private function syncIssue($issues, $boardId = null) {
      $worklog = array(); //for tasks with more 20 worklog registers

      foreach ($issues as $issue) {
         $tempIssue = $this->convertJiraTaskData($issue);
         if (!is_null($boardId)) {
            $tempIssue['board_id'] = $boardId;
         }

         Task::updateOrCreate(['id' => $tempIssue['id']], $tempIssue);
         $this->returnData['tasks'] ++;
         //Remove worklog to add again, because he can excluded in Jira
         TaskWorkLog::where('task_id', $issue['id'])->delete();

         if ($issue['fields']['worklog']['total'] > 0) {
            if ($issue['fields']['worklog']['total'] <= 20) {
               $this->syncWorklog($issue['fields']['worklog']['worklogs']);
            } else {
               array_push($worklog, $issue['key']);
            }
         }
      }
      foreach ($worklog as $value) {
         $this->syncWorklog(array(), $value);
      }
   }

   private function syncWorklog($worklogs, $taskKey = false) {
      if (is_array($worklogs) && count($worklogs) > 0) {
         foreach ($worklogs as $worklog) {
            $tempWorkLog = $this->convertJiraWorklogData($worklog);
            TaskWorkLog::updateOrCreate(['id' => $tempWorkLog['id']], $tempWorkLog);
            $this->returnData['worklogs'] ++;
         }
      } else if ($taskKey) {
         $data = $this->getCommonMethodData('Worklog', array('key' => $taskKey));
         $response = RequestMethod::sendRequest($data);

         if ($response['http_code'] == '200') {
            $issueWorklog = json_decode($response['response_body'], true);
            $this->syncWorklog($issueWorklog['worklogs']);
            return response()->json('OK!');
         } else {
            return response()->json('Fail');
         }
      }
   }

   public function syncAll(Request $request) {
      $this->syncUsers();
      $this->syncAllBoards();

      $boards = Board::where('sync_board', 'Y')
              ->get();

      foreach ($boards as $board) {
         $success = $this->getAllIssuesFromBoard($board['id']);
         //error_log(__CLASS__ . ' - ' . __FUNCTION__ . ' - '. __LINE__ . ' - ' .  $success);
         if ($success !== true) {
            $this->returnData['success'] = false;
            return response()->json($this->returnData);
         }
      }
      $this->returnData['success'] = true;
      return response()->json($this->returnData);
   }

   public function login(Request $request) {
      $requestData = $request->only(['email', 'api_key']);
      $error = false;

      if (!isset($requestData['email']) || is_null($requestData['email'])) {
         $error = 'E-mail inválido\n';
      }
      if (!isset($requestData['api_key']) || is_null($requestData['api_key'])) {
         $error .= 'API Key inválida\n';
      }

      if ($error) {
         return response()->json($error, 400);
      }

      $encoded = base64_encode($requestData['email'] . ':' . $requestData['api_key']);

      $data = $this->getCommonMethodData('MySelf', array(), $encoded);

      $response = RequestMethod::sendRequest($data);

      if ($response['http_code'] == '200') {
         $user = json_decode($response['response_body'], true);
         $returnData = array();
         $returnData['name'] = $user['displayName'];
         $returnData['token'] = $encoded;

         return response()->json($returnData);
      } else {
         return response()->json('Auth fail', $response['http_code']);
      }
   }

   private function convertJiraTaskData($jiraTaskData) {
      return array(
          'id' => $jiraTaskData['id'],
          'key' => $jiraTaskData['key'],
          'title' => $jiraTaskData['fields']['summary'],
          'assignee_key' => $jiraTaskData['fields']['assignee']['key'],
          'assignee_name' => $jiraTaskData['fields']['assignee']['displayName'],
          'tester_assignee_key' => isset($jiraTaskData['fields']['customfield_10060']['key']) ? $jiraTaskData['fields']['customfield_10060']['key'] : '',
          'tester_assignee_name' => isset($jiraTaskData['fields']['customfield_10060']['displayName']) ? $jiraTaskData['fields']['customfield_10060']['displayName'] : '',
          'jira_created_at' => Carbon::parse($jiraTaskData['fields']['created']),
          'initial_date' => $jiraTaskData['fields']['customfield_10110'],
          'deadline' => $jiraTaskData['fields']['customfield_10108'],
          'status_id' => $jiraTaskData['fields']['status']['id'],
          'status_name' => $jiraTaskData['fields']['status']['name'],
          'story_points' => isset($jiraTaskData['fields']['customfield_10043']) ? $jiraTaskData['fields']['customfield_10043'] : null,
          'finish_date' => isset($jiraTaskData['fields']['resolutiondate']) ? $jiraTaskData['fields']['resolutiondate'] : null,
          'status_category_id' => $jiraTaskData['fields']['status']['statusCategory']['id'],
          'status_category_name' => $jiraTaskData['fields']['status']['statusCategory']['name'],
          'test_initial_date' => $jiraTaskData['fields']['customfield_10112'],
          'test_deadline' => $jiraTaskData['fields']['customfield_10113'],
          'time_spent_seconds' => isset($jiraTaskData['fields']['timetracking']['timeSpentSeconds']) ? $jiraTaskData['fields']['timetracking']['timeSpentSeconds'] : null,
          'time_spent' => isset($jiraTaskData['fields']['timetracking']['timeSpent']) ? $jiraTaskData['fields']['timetracking']['timeSpent'] : null,
          'has_impediment' => isset($jiraTaskData['fields']['customfield_10115']['value']) ? 'Y' : 'N',
      );
   }

   private function convertJiraWorklogData($worklogData) {
      return array(
          'id' => $worklogData['id'],
          'task_id' => $worklogData['issueId'],
          'author_key' => $worklogData['author']['key'],
          'author_name' => $worklogData['author']['displayName'],
          'created_at_jira' => Carbon::parse($worklogData['created']),
          'time_spent_seconds' => $worklogData['timeSpentSeconds'],
          'time_spent' => $worklogData['timeSpent'],
          'comment' => isset($worklogData['comment']) ? $worklogData['comment'] : '',
          'started_at_jira' => Carbon::parse($worklogData['started']),
      );
   }

   private function getBoardId($projectId) {
      $board = Board::where('project_id', '=', $projectId)->firstOrFail();

      if ($board) {
         return $board['id'];
      } else {
         return -1;
      }
   }

   public function jiraWebHook(Request $request) {
      //error_log(json_encode($request->getContent()));
      $data = json_decode($request->getContent(), true);
      error_log('event: ' . $data['webhookEvent']);
      switch ($data['webhookEvent']) {
         case 'jira:issue_created':
            $this->insertTaskJiraWebhook($data['issue']);
            break;
         case 'jira:issue_updated':
            $this->updateTaskJiraWebhook($data['issue']);
            break;
         case 'jira:issue_deleted':
            $this->deleteTaskJiraWebhook($data['issue']);
            break;
         case 'worklog_created':
            $this->insertWorklogJiraWebhook($data['worklog']);
            break;
         case 'worklog_updated':
            $this->updateWorklogJiraWebhook($data['worklog']);
            break;
         case 'worklog_deleted':
            $this->deleteWorklogJiraWebhook($data['worklog']);
            break;
      }

      return response()->json($data['webhookEvent']);
//      DB::table('test')->insert(
//              ['content' => $request->getContent()]
//      );
   }

   //Task functions
   private function insertTaskJiraWebhook($issueData) {
      $convertedTask = $this->convertJiraTaskData($issueData);
      $convertedTask['board_id'] = $this->getBoardId($issueData['fields']['project']['id']);

      Task::create($convertedTask);
   }

   private function updateTaskJiraWebhook($issueData) {
      $task = Task::find($issueData['id']);
      $task->update($this->convertJiraTaskData($issueData));
   }

   private function deleteTaskJiraWebhook($issueData) {
      $task = Task::find($issueData['id']);
      TaskWorkLog::where('task_id', $issueData['id'])->delete();
      $task->delete();
   }

   //Worlog functions
   private function insertWorklogJiraWebhook($worklogData) {
      TaskWorkLog::create($this->convertJiraWorklogData($worklogData));
   }

   private function updateWorklogJiraWebhook($worklogData) {
      $taskWorklog = TaskWorkLog::find($worklogData['id']);
      $taskWorklog->update($this->convertJiraWorklogData($worklogData));
   }

   private function deleteWorklogJiraWebhook($worklogData) {
      $taskWorklog = TaskWorkLog::find($worklogData['id']);
      $taskWorklog->delete();
   }

   public function test() {

      $nonCompliances = DB::select("SELECT 
                                          u.name,
                                          u.email,
                                          nc.id,
                                          nc.date,
                                          nc.description,
                                          nc.severity, 
                                          nc.impact,
                                          nc.action_plan,
                                          nc.type,
                                          case
                                             when nc.severity = 'L' then '#70df6f;'
                                             when nc.severity = 'M' then ''
                                             when nc.severity = 'H' then ''
                                              end as severity_color
                                      FROM
                                          jira_task_manager.non_compliance nc
                                      INNER JOIN jira_task_manager.user u
                                         ON u.id = nc.user_id
                                      WHERE nc.notified = 'N'");
      $nonCompliance = \App\Models\NonCompliance::find(1);

      Mail::to('falercia@gmail.com')->send(new \App\Mail\SendMail($nonCompliance));
   }

}
