<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class APagarANegocio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('cheques_regalo', function (Blueprint $table) {
            $table->decimal('a_pagar_al_negocio', 10,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cheques_regalo', function (Blueprint $table) {
            $table->dropColumn('a_pagar_al_negocio');
        });
    }
}
