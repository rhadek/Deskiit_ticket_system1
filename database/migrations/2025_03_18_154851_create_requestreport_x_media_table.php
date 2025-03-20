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
        Schema::create('requestreport_x_media', function (Blueprint $table) {
            $table->foreignId('id_requestreport')->constrained('request_report');
            $table->foreignId('id_media')->constrained('media');
            $table->primary(['id_requestreport', 'id_media']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestreport_x_media');
    }
};
