<?php

namespace Common;

/**
 *
 * @author Fabio Garcia
 */
class MethodDefinition {

   private $baseUrl;
   private $issueFields;

   public function __construct() {
      $this->baseUrl = env('JIRA_BASE_URL');
      /**
       * customfield_10110: initial_date
       * customfield_10108: deadline
       * customfield_10060: tester_assignee_key
       * customfield_10043: story_points
       * customfield_10111: finish_date
       * customfield_10112: test_initial_date
       * customfield_10113: test_deadline
       * customfield_10115: has_impediment
       */
      $this->issueFields = 'assignee,description,summary,status,customfield_10110,customfield_10108,customfield_10060,worklog,created,customfield_10043,customfield_10111,customfield_10112,customfield_10113,timetracking,customfield_10115&maxResults=500';
   }

   public function getMethod($data = array()) {
      return ['AllIssuesFromBoard' => $this->getAllIssuesFromBoard(isset($data['board_id']) ? $data['board_id'] : null),
          'AllBoards' => $this->getAllBoards(),
          'AllUsers' => $this->getUsers(),
          'Issue' => $this->getIssue(isset($data['key']) ? $data['key'] : null),
          'MySelf' => $this->getMySelf(),
          'Worklog' => $this->getWorklog(isset($data['key']) ? $data['key'] : null),
      ];
   }

   private function getAllBoards() {
      return ['url' => $this->baseUrl . '/rest/agile/1.0/board?type=kanban',
          'http_verb' => 'GET'
      ];
   }

   private function getAllIssuesFromBoard($boardId) {
      return ['url' => $this->baseUrl . '/rest/agile/latest/board/' . $boardId . '/issue?fields=' . $this->issueFields . '&jql=%22Finish%20date%22%20is%20null',
          'http_verb' => 'GET'
      ];
   }

   private function getIssue($key) {
      return ['url' => $this->baseUrl . '/rest/api/2/issue/' . $key . '/?fields=' . $this->issueFields,
          'http_verb' => 'GET'
      ];
   }

   private function getUsers() {
      return ['url' => $this->baseUrl . '/rest/api/latest/user/search?startAt=0&maxResults=1000&username=%',
          'http_verb' => 'GET'
      ];
   }

   /**
    * Return current user data. Used by test credentials
    */
   private function getMySelf() {
      return ['url' => $this->baseUrl . '/rest/api/2/myself',
          'http_verb' => 'GET'
      ];
   }

   private function getWorklog($key) {
      return ['url' => $this->baseUrl . '/rest/api/2/issue/' . $key . '/worklog',
          'http_verb' => 'GET'
      ];
   }

   public function getHeaders($token) {
      return array(
          'Content-Type: application/json',
          'Authorization: Basic ' . $token
      );
   }

}
