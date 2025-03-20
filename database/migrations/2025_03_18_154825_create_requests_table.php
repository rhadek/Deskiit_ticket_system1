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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_projectitem')->constrained('project_items');
            $table->foreignId('id_custuser')->constrained('customer_users');
            $table->timestamp('inserted')->useCurrent();
            $table->smallInteger('state');
            $table->smallInteger('kind');
            $table->string('name', 100);
            $table->string('description', 1000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
