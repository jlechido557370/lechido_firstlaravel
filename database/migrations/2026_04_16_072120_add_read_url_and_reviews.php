<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add read_url column to books table
        Schema::table('books', function (Blueprint $table) {
            $table->string('read_url')->nullable()->after('description');
        });

        // Create book_reviews table
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1 to 5
            $table->text('comment')->nullable();
            $table->timestamps();

            // One review per user per book
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('read_url');
        });
    }
};