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
         $table->enum('notified', ['Y', 'N'])->default('N');
         $table->date('notified_date')->nullable();
         $table->enum('severity', ['L', 'M', 'H']);
         $table->string('action_plan', 2000)->nullable();
         $table->string('employee_comment', 2000)->nullable();
         $table->date('employee_comment_date')->nullable();
         $table->enum('type', ['AF', 'MP', 'DF', 'TF'])
                 ->nullable()
                 ->comment('AF: Attention Fault; MP: Merge Problem; DF: Development Fault; TF: Test Fault');

         $table->timestamps();
      });
   }

   public function down() {
      Schema::dropIfExists('non_compliance');
   }

}
