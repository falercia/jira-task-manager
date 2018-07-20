<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

   protected $fillable = ['id', 'board_id', 'key', 'title', 'assignee_key', 'assignee_name', 'created_at_jira', 'initial_date', 'deadline', 'status_id', 'status_name'];
   protected $table = 'task';

}
