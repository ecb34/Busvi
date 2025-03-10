<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longtext('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('address');
            $table->string('logo')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->integer('sector_id')->unsigned();
            $table->json('schedule')->nullable();
            // $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')
                                         ->on('companies')
                                         ->onDelete('cascade');
        });

        // Schema::table('companies', function (Blueprint $table) {
        //     $table->foreign('user_id')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
