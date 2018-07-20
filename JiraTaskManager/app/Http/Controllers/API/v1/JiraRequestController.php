<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Common\MethodDefinition;
use Common\RequestMethod;
use App\Models\Task;
use App\Models\TaskWorkLog;
use App\Models\Board;
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
                'created_at_jira' => Carbon::parse($issue['fields']['created']),
                'initial_date' => $issue['fields']['customfield_10110'],
                'deadline' => $issue['fields']['customfield_10108'],
                'status_id' => $issue['fields']['status']['id'],
                'status_name' => $issue['fields']['status']['name'],
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
      $this->getAllBoards();

      $boards = Board::where('sync_board', 'Y')
              ->get();

      foreach ($boards as $board) {
         $this->syncAllIssuesFromBoard($board['id']);
      }

      return response()->json('OK');
   }

}
