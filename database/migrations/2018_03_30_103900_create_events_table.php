<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')
                                      ->on('users')
                                      ->onDelete('set null');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')
                                         ->on('users')
                                         ->onDelete('set null');
            $table->integer('service_id')->unsigned()
                                         ->nullable();
            $table->foreign('service_id')->references('id')
                                         ->on('services')
                                         ->onDelete('set null');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->tinyInteger('status')->nullable()
                                         ->comment('0 = rechazada, 1 = aceptada, NULL = a la espera');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
