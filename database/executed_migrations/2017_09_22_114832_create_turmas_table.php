<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurmasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        
        Schema::create('turmas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('programa');
            $table->unsignedInteger('curso');
            $table->unsignedInteger('disciplina')->nullable();
            $table->unsignedInteger('professor');
            $table->unsignedInteger('local');            
            $table->string('dias_semana',25);
            $table->date('data_inicio');
            $table->date('data_termino')->nullable();
            $table->time('hora_inicio');
            $table->time('hora_termino');
            $table->decimal('valor',10,5);
            $table->unsignedInteger('vagas');
            $table->unsignedInteger('status');
            $table->string('atributos',20)->nullable();
            $table->unsignedInteger('3')->nullable();
            $table->timestampsTz();
            $table->foreign('programa')->references('id')->on('programas')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('curso')->references('id')->on('cursos')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('disciplina')->references('id')->on('disciplinas')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('professor')->references('id')->on('pessoas')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('local')->references('id')->on('locais')->onDelete('restrict')->onUpdate('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('turmas');
        Schema::dropIfExists('locais');
    }
}
