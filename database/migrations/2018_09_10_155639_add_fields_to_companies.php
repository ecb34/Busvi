<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('province')->nullable()->after('cp');
            $table->string('name_comercial')->nullable()->after('name');
            $table->string('bank_count')->nullable()->after('schedule');
            $table->string('card_number')->nullable()->after('bank_count');
            $table->string('phone2')->nullable()->after('phone');
        });
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
