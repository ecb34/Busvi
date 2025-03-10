<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Aforo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('eventos', function (Blueprint $table) {
            $table->integer('aforo_maximo')->nullable();
            $table->decimal('long', 10, 7)->nullable()->change();
            $table->decimal('lat', 10, 7)->nullable()->change();   
        });

        Schema::table('cliente_evento', function (Blueprint $table) {
            $table->integer('plazas_reservadas');
        });    

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('aforo_maximo');
            $table->decimal('long', 10, 7)->nullable(false)->change();
            $table->decimal('lat', 10, 7)->nullable(false)->change();               
        });

        Schema::table('cliente_evento', function (Blueprint $table) {
            $table->dropColumn('plazas_reservadas');
        });    
    }
}