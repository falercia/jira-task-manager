<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

   protected $fillable = ['id', 'board_id', 'key', 'title', 'assignee_key', 'assignee_name', 'tester_assignee_key', 'tester_assignee_name', 'jira_created_at', 'initial_date', 'deadline',
       'status_id', 'status_name', 'story_points', 'finish_date', 'status_category_id', 'status_category_name', 'test_initial_date', 'test_deadline', 'time_spent', 'time_spent_seconds',
       'has_impediment'
       ];
   protected $table = 'task';

}
