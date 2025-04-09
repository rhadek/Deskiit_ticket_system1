<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_offers', function (Blueprint $table) {
            $table->smallInteger('approved')->default(0)->after('file_mime');
            $table->string('approved_by')->nullable()->after('approved');
        });
    }

    public function down(): void
    {
        Schema::table('request_offers', function (Blueprint $table) {
            $table->dropColumn(['approved', 'approved_by']);
        });
    }
};
