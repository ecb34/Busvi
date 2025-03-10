<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChequeRegalo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheques_regalo', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid'); 
            $table->decimal('importe', 10,2);
            $table->integer('from_user_id')->unsigned();
            $table->foreign('from_user_id')->references('id')
                                      ->on('users')
                                      ->onDelete('cascade');
            $table->integer('to_user_id')->unsigned()->nullable();
            $table->foreign('to_user_id')->references('id')
                                      ->on('users')
                                      ->onDelete('cascade');                          
            $table->integer('company_id')->unsigned()->nullable()->comment('bloquear a un solo comercio');
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
            $table->tinyInteger('status')->default(0)
                                         ->comment('0 = pendiente_pago, 1 = disponible, 2 = parcialmente_usado, 3 = usado');
            $table->dateTime('used_at')->nullable();                             
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
        Schema::dropIfExists('cheques_regalo');
    }
}
