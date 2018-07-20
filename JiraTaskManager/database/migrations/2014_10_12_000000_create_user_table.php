<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

   public function up() {
      Schema::create('user', function (Blueprint $table) {
         $table->string('id', 100);
         $table->string('name');
         $table->string('email')->unique();
         $table->string('password')->nullable();
         $table->string('jira_key', 500)->nullable();
         $table->enum('is_resource', ['Y', 'N'])->default('Y');
         $table->timestamps();

         $table->primary('id');
      });
   }

   public function down() {
      Schema::dropIfExists('user');
   }

}
