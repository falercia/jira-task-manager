<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenceTable extends Migration {

   public function up() {
      Schema::create('absence', function (Blueprint $table) {
         $table->increments('id');
         $table->string('user_id', 100);
         $table->datetime('initial_date');
         $table->datetime('final_date');
         $table->enum('type', ['PR', 'M', 'V', 'HM'])
                 ->comment('P: Personal reason; M: Medical; V: Vacation; HM: Home office');
         $table->string('comment', 1000);
         $table->timestamps();
      });
   }

   public function down() {
      Schema::dropIfExists('absence');
   }

}
