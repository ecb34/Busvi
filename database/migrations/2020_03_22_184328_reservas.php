<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Reservas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('companies', function(Blueprint $table){
            $table->boolean('enable_fichajes')->after('enable_events')->default(0);
            $table->boolean('enable_reservas')->after('enable_fichajes')->default(0);
        });

        Schema::create('turnos', function(Blueprint $table){
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('nombre')->default('');
            $table->time('inicio');
            $table->time('fin');
            $table->text('descripcion');
            $table->integer('plazas')->default(0);
            $table->boolean('lunes')->default(1);
            $table->boolean('martes')->default(1);
            $table->boolean('miercoles')->default(1);
            $table->boolean('jueves')->default(1);
            $table->boolean('viernes')->default(1);
            $table->boolean('sabado')->default(1);
            $table->boolean('domingo')->default(1);
        });

        Schema::create('bloqueos_turnos', function(Blueprint $table){
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('turno_id')->unsigned()->nullable();
            $table->foreign('turno_id')->references('id')->on('turnos')->onDelete('cascade');
            $table->date('fecha');
        });

        Schema::create('reservas', function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('turno_id')->unsigned();
            $table->foreign('turno_id')->references('id')->on('turnos')->onDelete('cascade');
            $table->date('fecha');
            $table->integer('plazas');
            $table->boolean('confirmado')->default('0');
            $table->boolean('anulado')->default('0');
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
        
        Schema::drop('reservas');
        Schema::drop('bloqueos_turnos');
        Schema::drop('turnos');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('enable_fichajes');
            $table->dropColumn('enable_reservas');
        });

    }
}
