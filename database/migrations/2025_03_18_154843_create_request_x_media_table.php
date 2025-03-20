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
        Schema::create('request_x_media', function (Blueprint $table) {
            $table->foreignId('id_request')->constrained('requests');
            $table->foreignId('id_media')->constrained('media');
            $table->primary(['id_request', 'id_media']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_x_media');
    }
};
