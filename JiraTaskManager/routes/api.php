<?php

$this->group(['prefix' => 'v1'], function() {
   $this->get('sync-issues-from-board/{boardId}', 'API\v1\JiraRequestController@syncAllIssuesFromBoard');
   $this->get('boards', 'API\v1\JiraRequestController@syncAllBoards');
   $this->get('users-no-task', 'API\v1\JiraRequestController@getUserNoTask');
   $this->get('get-issue/{key}', 'API\v1\JiraRequestController@getIssue');
   $this->get('test-get', 'API\v1\JiraRequestController@test');
   
   $this->post('sync-all', 'API\v1\JiraRequestController@syncAll');
});
