<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FechaUsoEntradaEvento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cliente_evento', function (Blueprint $table) {
            $table->datetime('ultimo_uso_entrada')->nullable();
            $table->uuid('uuid'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cliente_evento', function (Blueprint $table) {
            $table->dropColumn('ultimo_uso_entrada');
            $table->dropColumn('uuid');
        });
    }
}
