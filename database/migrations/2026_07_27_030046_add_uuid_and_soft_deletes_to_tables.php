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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->softDeletes();
        });
        Schema::table('asset_transactions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->softDeletes();
        });
        Schema::table('asset_attachments', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropSoftDeletes();
        });
        Schema::table('asset_transactions', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropSoftDeletes();
        });
        Schema::table('asset_attachments', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropSoftDeletes();
        });
    }
};
