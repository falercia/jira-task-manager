<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingTable extends Migration {

   public function up() {
      Schema::create('meeting', function (Blueprint $table) {
         $table->increments('id');
         $table->date('date');
         $table->string('title', 150);
         $table->string('subjects_discussed', 1000);
         $table->timestamps();
      });
   }

   public function down() {
      Schema::dropIfExists('meeting');
   }

}
