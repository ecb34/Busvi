<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Eventos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_evento', function (Blueprint $table) {
           $table->increments('id');  
           $table->string('nombre')->index();     
        });     

        Schema::create('eventos', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('nombre');
            $table->decimal('precio', 10,2)->default(0);
            $table->decimal('precio_final', 10,2)->default(0);
            $table->integer('categoria_evento_id')->unsigned()->nullable();
            $table->foreign('categoria_evento_id')->references('id')
                                      ->on('categorias_evento')
                                      ->onDelete('set null');
            $table->integer('organizador_id')->unsigned();
            $table->foreign('organizador_id')->references('id')
                                      ->on('users')
                                      ->onDelete('cascade');
            $table->integer('company_id')->unsigned()->nullable()->comment('local en el que se celebra');
            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');
            $table->string('direccion')->comment('donde se celebra');                  
            $table->string('poblacion')->comment('donde se celebra');                  
            $table->decimal('long', 10, 7);
            $table->decimal('lat', 10, 7);   
            $table->dateTime('desde');                             
            $table->dateTime('hasta')->nullable();  
            $table->boolean('validado')->default(0);
            $table->boolean('pagado_a_comercio')->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('cliente_evento', function (Blueprint $table) {
            $table->increments('id');            
            $table->decimal('precio', 10,2);
            $table->integer('evento_id')->unsigned()->nullable();
            $table->foreign('evento_id')->references('id')
                                      ->on('eventos')
                                      ->onDelete('cascade');
            $table->integer('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')
                                      ->on('users')
                                      ->onDelete('cascade');
            $table->boolean('pagado')->default(0);
            $table->boolean('confirmacion_asistencia')->default(0);   
            $table->timestamps();
        });                              

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('accept_eventos')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente_evento');
        Schema::dropIfExists('eventos');
        Schema::dropIfExists('categorias_evento');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('accept_eventos');
        });
    }
}
