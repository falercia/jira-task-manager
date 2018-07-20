<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskWorkLogTable extends Migration {

   public function up() {
      Schema::create('task_worklog', function (Blueprint $table) {
         $table->integer('id');
         $table->integer('task_id');
         $table->string('author_key', 300);
         $table->string('author_name', 300);
         $table->string('comment', 3000)->nullable();
         $table->date('created_at_jira');
         $table->bigInteger('time_spent_seconds');
         $table->timestamps();
         
         $table->primary('id');
      });
   }

   public function down() {
      Schema::dropIfExists('task_worklog');
   }

}
