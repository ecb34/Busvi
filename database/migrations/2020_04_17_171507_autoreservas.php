<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Autoreservas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservas', function(Blueprint $table){
            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->string('nombre')->default('')->after('fecha');
            $table->string('telefono')->default('')->after('nombre');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservas', function(Blueprint $table){
            $table->integer('user_id')->unsigned()->change();
            $table->dropColumn('nombre')->default('')->after('fecha');
            $table->dropColumn('telefono')->default('')->after('nombre');
        });
    }
}
