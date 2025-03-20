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
        Schema::create('custuser_x_projectitem', function (Blueprint $table) {
            $table->foreignId('id_custuser')->constrained('customer_users');
            $table->foreignId('id_projectitem')->constrained('project_items');
            $table->primary(['id_custuser', 'id_projectitem']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custuser_x_projectitem');
    }
};
