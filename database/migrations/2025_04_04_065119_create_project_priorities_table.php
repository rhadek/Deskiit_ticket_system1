<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_priorities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_project')->constrained('projects');
            $table->string('name', 100);
            $table->smallInteger('kind');
            $table->integer('execution_time_limit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_priorities');
    }
};
