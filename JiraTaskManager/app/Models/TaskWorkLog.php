<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWorkLog extends Model {

   protected $table = 'task_worklog';
   protected $fillable = ['id', 'task_id', 'author_key', 'author_name', 'created_at_jira', 'time_spent_seconds'];

}
