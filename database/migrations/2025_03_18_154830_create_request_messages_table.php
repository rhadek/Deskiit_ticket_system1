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
        Schema::create('request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_request')->constrained('requests');
            $table->foreignId('id_user')->nullable()->constrained('users');
            $table->foreignId('id_custuser')->nullable()->constrained('customer_users');
            $table->timestamp('inserted')->useCurrent();
            $table->smallInteger('state');
            $table->smallInteger('kind');
            $table->string('message', 1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_messages');
    }
};
