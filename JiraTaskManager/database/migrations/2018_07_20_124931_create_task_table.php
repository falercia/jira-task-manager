<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration {

   public function up() {
      Schema::create('task', function (Blueprint $table) {
         $table->integer('id'); //Sended by Jira
         $table->integer('board_id'); //Sended by Jira
         $table->string('key', 50)->unique();
         $table->string('title', 1000);
         $table->string('assignee_key', 1000)->nullable();
         $table->string('assignee_name', 1000)->nullable();
         $table->string('tester_assignee_key', 1000)->nullable();
         $table->string('tester_assignee_name', 1000)->nullable();
         $table->date('jira_created_at');
         $table->date('initial_date')->nullable();
         $table->date('deadline')->nullable();
         $table->integer('status_id');
         $table->string('status_name');

         $table->timestamps();

         $table->primary('id');
      });
   }

   public function down() {
      Schema::dropIfExists('task');
   }

}
