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
        Schema::create('data_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'assets', 'users', dst
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->enum('direction', ['export', 'import'])->default('export')->after('type');
            $table->json('summary')->nullable()->after('error_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_jobs');
    }
};
