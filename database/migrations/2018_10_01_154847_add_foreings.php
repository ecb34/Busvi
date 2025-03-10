<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('events', function (Blueprint $table) {
        //     $table->integer('service_id')->unsigned()
        //                                  ->nullable();
        //     $table->foreign('service_id')->references('id')
        //                                  ->on('sergices')
        //                                  ->onDelete('null');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
