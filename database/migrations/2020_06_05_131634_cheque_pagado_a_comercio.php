<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChequePagadoAComercio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('cheques_regalo', function (Blueprint $table) {
            $table->boolean('pagado_a_comercio')->default(0);
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
            $table->dropColumn('pagado_a_comercio');
        });
    }
}

