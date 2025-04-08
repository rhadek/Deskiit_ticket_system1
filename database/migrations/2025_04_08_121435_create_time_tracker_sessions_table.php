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
        Schema::create('time_tracker_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_request')->constrained('requests');
            $table->foreignId('id_user')->constrained('users');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('total_minutes')->nullable();
            $table->boolean('completed')->default(false);
            $table->boolean('report_created')->default(false); // Odstraněn after()
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_tracker_sessions');
    }
};
