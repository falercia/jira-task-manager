<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNonComplianceTable extends Migration {

   public function up() {
      Schema::create('non_compliance', function (Blueprint $table) {
         $table->increments('id');
         $table->string('user_id', 100);
         $table->string('description', 2000);
         $table->date('date');
         $table->enum('notified', ['Y', 'N']);
         $table->date('notified_date');
         $table->enum('severity', ['L', 'M', 'H']);
         $table->string('action_plan', 2000);
         $table->string('employee_comment', 2000);
         $table->date('employee_comment_date');

         $table->timestamps();
      });
   }

   public function down() {
      Schema::dropIfExists('non_compliance');
   }

}
