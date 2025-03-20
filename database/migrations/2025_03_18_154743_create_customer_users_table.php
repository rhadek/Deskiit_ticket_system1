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
        Schema::create('customer_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_customer')->constrained('customers');
            $table->smallInteger('state');
            $table->smallInteger('kind');
            $table->string('username', 100);
            $table->string('password', 100);
            $table->string('fname', 100);
            $table->string('lname', 100);
            $table->string('email', 100);
            $table->string('telephone', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_users');
    }
};
