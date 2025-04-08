<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_offers', function (Blueprint $table) {
            // Přidání nových sloupců
            $table->string('file_path')->nullable()->after('file');
            $table->string('file_name')->nullable()->after('file_path');
            $table->string('file_mime')->nullable()->after('file_name');
        });
    }

    public function down(): void
    {
        Schema::table('request_offers', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name', 'file_mime']);
        });
    }
};
