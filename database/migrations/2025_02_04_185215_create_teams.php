<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fases', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha_apertura');
            $table->timestamps();
        });

        Schema::create('concursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('modalidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('concurso_id')->constrained('concursos')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('lineas_investigacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('fase_id')->constrained('fases')->onDelete('cascade');
            $table->foreignId('modalidad_id')->constrained('modalidades')->onDelete('cascade');
            $table->foreignId('linea_investigacion_id')->constrained('lineas_investigacion')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('asesores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('telefono');
            $table->foreignId('equipo_id')->unique()->nullable()->constrained('equipos')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('categoria', ['Alumno', 'Docente']);
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
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
