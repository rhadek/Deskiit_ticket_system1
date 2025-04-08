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
        Schema::create('request_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_request')->constrained('requests');
            $table->foreignId('id_user')->constrained('users');
            $table->timestamp('inserted')->useCurrent();
            $table->smallInteger('state');
            $table->smallInteger('kind');
            $table->timestamp('work_start')->nullable();
            $table->timestamp('work_end')->nullable();
            $table->integer('work_total');
            $table->string('description', 1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_report');
    }
};
