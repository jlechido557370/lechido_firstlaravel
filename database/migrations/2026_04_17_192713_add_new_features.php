<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('gender')->nullable()->after('bio'); // male, female, prefer_not_to_say
            $table->boolean('allow_dms')->default(true)->after('gender');
        });

        // User follows user
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('following_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['follower_id', 'following_id']);
            $table->timestamps();
        });

        // User follows author (by name string)
        Schema::create('author_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->unique(['user_id', 'author_name']);
            $table->timestamps();
        });

        // User-submitted books (requires approval)
        Schema::create('user_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->string('genre');
            $table->unsignedInteger('published_year')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('read_url')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Direct messages
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('user_books');
        Schema::dropIfExists('author_follows');
        Schema::dropIfExists('follows');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'gender', 'allow_dms']);
        });
    }
};