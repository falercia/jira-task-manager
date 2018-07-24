<?php

$this->group(['prefix' => 'v1'], function() {
   $this->get('sync-issues-from-board/{boardId}', 'API\v1\JiraRequestController@syncAllIssuesFromBoard');
   $this->get('boards', 'API\v1\JiraRequestController@getAllBoards');
   $this->get('users-no-task', 'API\v1\JiraRequestController@getUserNoTask');
   
   $this->post('sync-all', 'API\v1\JiraRequestController@syncAll');
});
