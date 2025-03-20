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
        Schema::create('requestmessage_x_media', function (Blueprint $table) {
            $table->foreignId('id_requestmessage')->constrained('request_messages');
            $table->foreignId('id_media')->constrained('media');
            $table->primary(['id_requestmessage', 'id_media']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestmessage_x_media');
    }
};
