<?php

namespace Common;

/**
 *
 * @author Fabio Garcia
 */
class MethodDefinition {

   private $token;
   private $baseUrl;

   public function __construct() {
      $this->token = env('JIRA_BASIC_AUTH');
      $this->baseUrl = env('JIRA_BASE_URL');
   }

   public function getMethod($data = array()) {
      return ['AllIssuesFromBoard' => $this->getAllIssuesFromBoard(isset($data['board_id']) ? $data['board_id'] : null),
          'AllBoards' => $this->getAllBoards(),
          'AllUsers' => $this->getUsers(),
      ];
   }

   private function getAllBoards() {
      return ['url' => $this->baseUrl . '/rest/agile/1.0/board?type=kanban',
          'http_verb' => 'GET'
      ];
   }

   
   /*
    */
   private function getAllIssuesFromBoard($boardId) {
      return ['url' => $this->baseUrl . '/rest/agile/latest/board/' . $boardId . '/issue?fields=assignee,description,summary,status,customfield_10110,customfield_10108,customfield_10060,worklog,created,customfield_10043,customfield_10111',
          'http_verb' => 'GET'
      ];
   }

   private function getUsers() {
      return ['url' => $this->baseUrl . '/rest/api/latest/user/search?startAt=0&maxResults=1000&username=%',
          'http_verb' => 'GET'
      ];
   }

   public function getHeaders() {
      return array(
          'Content-Type: application/json',
          'Authorization: Basic ' . $this->token
      );
   }

}
