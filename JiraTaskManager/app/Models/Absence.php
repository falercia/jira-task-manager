<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model {

   protected $table = 'absence';
   protected $fillable = ['user_id', 'initial_date', 'final_date', 'type', 'comment'];

}
