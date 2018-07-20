<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

   use Notifiable;

   protected $table = 'user';
   protected $fillable = [
       'id', 'name', 'email', 'jira_key', 'is_resource',
   ];
   protected $hidden = [
       'password', 'remember_token',
   ];

}
