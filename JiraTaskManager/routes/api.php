<?php

$this->group(['prefix' => 'v1'], function() {
   $this->get('sync-issues-from-board/{boardId}', 'API\v1\JiraRequestController@syncAllIssuesFromBoard');
   $this->get('boards', 'API\v1\JiraRequestController@getAllBoards');
   
   $this->post('sync-all', 'API\v1\JiraRequestController@syncAll');
});
