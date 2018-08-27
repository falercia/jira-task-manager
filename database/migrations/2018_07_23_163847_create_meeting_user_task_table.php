<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingUserTaskTable extends Migration {

   public function up() {
      Schema::create('meeting_user_task', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('meeting_user_id');
         $table->integer('task_id')->nullable();
         $table->enum('has_impediment', ['Y', 'N'])
                 ->default('N')
                 ->nullable();
         $table->enum('comment_type', ['D', 'IM', 'WP'])
                 ->comment('D: Delay; IM: Impediment; IM: Within the Planned');
         $table->string('comment', 500)->nullable();
         $table->timestamps();
      });
   }

   public function down() {
      Schema::dropIfExists('meeting_user_task');
   }

}
