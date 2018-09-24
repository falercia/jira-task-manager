<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model {

   protected $table = 'board';
   protected $fillable = ['id', 'name', 'type', 'project_id'];

}
