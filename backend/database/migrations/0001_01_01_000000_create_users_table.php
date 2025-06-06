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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID instead of auto-increment ID
            $table->enum('type', ['Admin', 'Freelancer', 'Client'])->default('Freelancer');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('profile_picture')->nullable();
            $table->string('payment_phone')->nullable();
            $table->decimal('balance', 10, 2)->default(0.00);
            // Add google_id column here and make it nullable
            $table->string('google_id')->nullable()->unique(); // nullable and unique to prevent duplicate google_id
            $table->string('avatar')->nullable(); // If you want to store the avatar as well
            $table->unsignedBigInteger('payment_history_id')->nullable();
            $table->string('nationality')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
