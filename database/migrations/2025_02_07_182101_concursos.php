<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('fases')) {
            Schema::create('fases', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->date('fecha_apertura');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('concursos')) {
            Schema::create('concursos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('modalidades')) {
            Schema::create('modalidades', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->foreignId('concurso_id')->constrained('concursos')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lineas_investigacion')) {
            Schema::create('lineas_investigacion', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('proyectos')) {
            Schema::create('proyectos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->foreignId('fase_id')->constrained('fases')->onDelete('cascade');
                $table->foreignId('modalidad_id')->constrained('modalidades')->onDelete('cascade');
                $table->foreignId('linea_investigacion_id')->constrained('lineas_investigacion')->onDelete('cascade');
                $table->foreignId('concurso_id')->constrained('concursos')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('equipos')) {
            Schema::create('equipos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
                $table->foreignId('concurso_id')->constrained('concursos')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('asesores')) {
            Schema::create('asesores', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('email')->unique();
                $table->string('telefono');
                $table->foreignId('equipo_id')->unique()->nullable()->constrained('equipos')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('participantes')) {
            Schema::create('participantes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->enum('categoria', ['Alumno', 'Docente']);
                $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('documentaciones')) {
            Schema::create('documentaciones', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('ruta');
                $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('documentaciones');
        Schema::dropIfExists('participantes');
        Schema::dropIfExists('asesores');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('proyectos');
        Schema::dropIfExists('lineas_investigacion');
        Schema::dropIfExists('modalidades');
        Schema::dropIfExists('concursos');
        Schema::dropIfExists('fases');
    }
};
