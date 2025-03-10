<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndicesTurnos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bloqueos_turnos', function ($table) {
            $table->index(['company_id', 'fecha']);
            $table->index(['company_id', 'turno_id', 'fecha']);
        });
        Schema::table('reservas', function ($table) {
            $table->index(['anulado', 'turno_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bloqueos_turnos', function ($table) {
            $table->index(['company_id', 'fecha']);
            $table->dropIndex(['company_id', 'turno_id', 'fecha']);
        });
        Schema::table('reservas', function ($table) {
            $table->dropIndex(['anulado', 'turno_id', 'fecha']);
        });
    }
}
