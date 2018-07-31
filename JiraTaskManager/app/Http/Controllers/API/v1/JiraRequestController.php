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

class JiraRequestController extends Controller {

   private $methodDefinition;

   public function __construct() {
      $this->methodDefinition = new MethodDefinition();
   }

   private function getCommonMethodData($method, $data = array()) {
      $data['url'] = $this->methodDefinition->getMethod($data)[$method]['url'];
      $data['http_verb'] = $this->methodDefinition->getMethod($data)[$method]['http_verb'];
      $data['headers'] = $this->methodDefinition->getHeaders();

      return $data;
   }

   public function syncUsers() {
      $data = $this->getCommonMethodData('AllUsers');

      $response = RequestMethod::sendRequest($data);


      if ($response['http_code'] == '200') {
         $users = json_decode($response['response_body'], true);
         foreach ($users as $user) {
            if (strpos($user['emailAddress'], '@hubchain.io')) {
               $tempUser = array(
                   'id' => $user['accountId'],
                   'name' => $user['displayName'],
                   'email' => $user['emailAddress'],
                   'jira_key' => $user['key'],
                   'is_resource' => in_array($user['emailAddress'], array('alex.ritchie@hubchain.io', 'admin@hubchain.io', 'rodrigo.pimenta@hubchain.io')) ? 'N' : 'Y',
               );

               User::updateOrCreate(['id' => $tempUser['id']], $tempUser);
            }
         }
      }
   }

   public function getAllBoards() {
      $data = $this->getCommonMethodData('AllBoards');

      $response = RequestMethod::sendRequest($data);


      if ($response['http_code'] == '200') {
         $boards = json_decode($response['response_body'], true)['values'];
         foreach ($boards as $board) {
            $tempBoard = array(
                'id' => $board['id'],
                'name' => $board['location']['name'],
                'type' => $board['type'],
            );

            Board::updateOrCreate(['id' => $tempBoard['id']], $tempBoard);
         }
      }
   }

   public function syncAllIssuesFromBoard($boardId) {

      $data = $this->getCommonMethodData('AllIssuesFromBoard', array('board_id' => $boardId));

      $response = RequestMethod::sendRequest($data);

      if ($response['http_code'] == '200') {
         $issues = json_decode($response['response_body'], true)['issues'];
         foreach ($issues as $issue) {
            $tempIssue = array(
                'id' => $issue['id'],
                'board_id' => $boardId,
                'key' => $issue['key'],
                'title' => $issue['fields']['summary'],
                'assignee_key' => $issue['fields']['assignee']['key'],
                'assignee_name' => $issue['fields']['assignee']['displayName'],
                'tester_assignee_key' => isset($issue['fields']['customfield_10060']['key']) ? $issue['fields']['customfield_10060']['key'] : '',
                'tester_assignee_name' => isset($issue['fields']['customfield_10060']['displayName']) ? $issue['fields']['customfield_10060']['displayName'] : '',
                'jira_created_at' => Carbon::parse($issue['fields']['created']),
                'initial_date' => $issue['fields']['customfield_10110'],
                'deadline' => $issue['fields']['customfield_10108'],
                'status_id' => $issue['fields']['status']['id'],
                'status_name' => $issue['fields']['status']['name'],
                'story_points' => isset($issue['fields']['customfield_10043']) ? $issue['fields']['customfield_10043'] : null,
                'finish_date' => isset($issue['fields']['customfield_10111']) ? $issue['fields']['customfield_10111'] : null,
                'status_category_id' => $issue['fields']['status']['statusCategory']['id'],
                'status_category_name' => $issue['fields']['status']['statusCategory']['name'],
                'test_initial_date' => $issue['fields']['customfield_10112'],
                'test_deadline' => $issue['fields']['customfield_10113'],
                'time_spent_seconds' => isset($issue['fields']['timetracking']['timeSpentSeconds']) ? $issue['fields']['timetracking']['timeSpentSeconds'] : null,
                'time_spent' => isset($issue['fields']['timetracking']['timeSpent']) ? $issue['fields']['timetracking']['timeSpent'] : null,
                
            );

            Task::updateOrCreate(['id' => $tempIssue['id']], $tempIssue);

            if ($issue['fields']['worklog']['total'] > 0) {
               foreach ($issue['fields']['worklog']['worklogs'] as $worklog) {
                  $tempWorkLog = array(
                      'id' => $worklog['id'],
                      'task_id' => $worklog['issueId'],
                      'author_key' => $worklog['author']['key'],
                      'author_name' => $worklog['author']['displayName'],
                      'created_at_jira' => Carbon::parse($worklog['created']),
                      'time_spent_seconds' => $worklog['timeSpentSeconds'],
                      'time_spent' => $worklog['timeSpent'],
                      'comment' => isset($worklog['comment']) ? $worklog['comment'] : '',
                      'started_at_jira' => Carbon::parse($worklog['started']),
                  );

                  TaskWorkLog::updateOrCreate(['id' => $tempWorkLog['id']], $tempWorkLog);
               }
            }
         }
         return response()->json('OK!');
      } else {
         return response()->json('Fail');
      }
   }

   public function syncAll(Request $request) {
      $this->syncUsers();
      $this->getAllBoards();

      $boards = Board::where('sync_board', 'Y')
              ->get();

      foreach ($boards as $board) {
         $this->syncAllIssuesFromBoard($board['id']);
      }

      return response()->json('OK');
   }

}
