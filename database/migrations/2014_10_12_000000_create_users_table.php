<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('address')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('genere')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('img')->nullable();
            $table->string('cp')->nullable();
            $table->string('password');
            // $table->integer('company_id')->unsigned()->nullable();
            $table->enum('role', ['superadmin', 'operator', 'admin', 'crew', 'user']);
            $table->rememberToken();
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
