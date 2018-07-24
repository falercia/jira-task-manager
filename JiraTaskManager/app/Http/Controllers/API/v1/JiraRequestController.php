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
                      'comment' => isset($worklog['comment']) ? $worklog['comment'] : '',
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

   public function getUserNoTask() {
      $indicator = array();
      $indicator['id'] = 'users_no_task';
      $indicator['title'] = 'Usuários sem tarefa';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('ID', 'Nome', 'E-mail');
      $indicator['tooltip'] = 'Usuários que estão sem tarefas atribuidas no status In Progress ou que estão testando tarefa sem estar atribuido como Tester.';

      $indicator['data'] = DB::select('SELECT 
                              u.id, u.name, u.email
                          FROM
                              jira_task_manager.user u
                           WHERE u.is_resource = \'Y\' 
                            AND NOT EXISTS (select 1
                                               from jira_task_manager.task t
                                                                  where (t.assignee_key = u.jira_key and t.status_id = 3)
                                                 or (t.status_id = 10004 and t.tester_assignee_key = u.jira_key)
                  )');

      return $indicator;
   }

   public function getTasksWithoutDate() {
      $indicator = array();
      $indicator['id'] = 'tasks_without_date';
      $indicator['title'] = 'Tarefas sem prazo';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('ID', 'Key', 'Tarefa', 'Atribuída a', 'ID Status', 'Status');
      $indicator['tooltip'] = 'Tarefas que estão sem prazo inicial ou final definidos (todos os status).';

      $indicator['data'] = DB::select('SELECT 
                                             t.id,
                                             t.key,
                                             t.title,
                                             t.assignee_name,
                                             t.status_id,
                                             t.status_name
                                         FROM
                                             jira_task_manager.task t
                                         WHERE
                                             (t.initial_date IS NULL
                                                 OR t.deadline IS NULL)');

      return $indicator;
   }

   public function getTasksRunningWithoutDate() {
      $indicator = array();
      $indicator['id'] = 'tasks_running_without_date';
      $indicator['title'] = 'Tarefas em produção sem prazo';
      $indicator['icon'] = 'fa fa-fw fa-file';
      $indicator['color'] = '#c7ddef';
      $indicator['columns'] = array('ID', 'Nome', 'E-mail');
      $indicator['tooltip'] = 'São tarefas que estão sendo executadas sem prazo no estado In Progress ou em teste.';

      $indicator['data'] = DB::select('SELECT 
                                             t.id as ID,
                                             t.key as "Key",
                                             t.title as "Título",
                                             t.assignee_name as "Atribuida a",
                                             t.status_id as "ID Status",
                                             t.status_name as "Status"
                                      FROM
                                          jira_task_manager.task t
                                      WHERE
                                          (t.initial_date IS NULL OR t.deadline IS NULL)
                                       AND t.status_name in (\'Test\', \'In Progress\')');

      return $indicator;
   }

   public function getIndicators() {
      $indicators = array();

      array_push($indicators, $this->getUserNoTask());
      array_push($indicators, $this->getTasksWithoutDate());
      array_push($indicators, $this->getTasksRunningWithoutDate());
      
     // dd($indicators);

      return view('admin.indicators', compact('indicators'));
   }

}
