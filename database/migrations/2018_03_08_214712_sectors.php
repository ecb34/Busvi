<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sectors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('img')->nullable();
            $table->integer('sector_parent_id');
            // $table->softDeletes();
        });

        // Schema::table('companies', function (Blueprint $table) {
        //     $table->foreign('sector_id')
        //           ->references('id')
        //           ->on('sectors');
        //           ->onDelete('set null');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sectors');
    }
}
