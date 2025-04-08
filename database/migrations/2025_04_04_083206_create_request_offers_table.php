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
        Schema::create('request_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_request')->constrained('requests');
            $table->integer('price');
            $table->string('name', 100);
            $table->binary('file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_offers');
    }
};
