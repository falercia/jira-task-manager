<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingUserTable extends Migration {

   public function up() {
      Schema::create('meeting_user', function (Blueprint $table) {
         $table->increments('id');
         $table->string('user_id', 100);
         $table->integer('meeting_id');
         $table->enum('was_present', ['Y', 'N'])->default('Y');
         $table->string('reason_not_present', 300)->default('Y');

         $table->timestamps();
      });
   }

   public function down() {
      Schema::dropIfExists('meeting_user');
   }

}
