<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ContadoresEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('phone_counter')->unsigned()->default(0);
            $table->integer('email_counter')->unsigned()->default(0);
            $table->integer('map_counter')->unsigned()->default(0);
            $table->integer('web_counter')->unsigned()->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('phone_counter');
            $table->dropColumn('email_counter');
            $table->dropColumn('map_counter');
            $table->dropColumn('web_counter');
        });

    }
}
