<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardTable extends Migration {

   public function up() {
      Schema::create('board', function (Blueprint $table) {
         $table->integer('id');
         $table->string('name', 500);
         $table->string('type', 500);
         $table->enum('sync_board', ['Y', 'N'])->default('Y');
         $table->timestamps();

         $table->primary('id');
      });
   }

   public function down() {
      Schema::dropIfExists('board');
   }

}
