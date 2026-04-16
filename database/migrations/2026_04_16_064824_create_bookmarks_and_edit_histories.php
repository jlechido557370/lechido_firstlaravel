<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'book_id']);
        });

        Schema::create('book_edit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('field_changed');          // e.g. 'title', 'genre', 'copies'
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('action')->default('updated'); // updated, created
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_edit_histories');
        Schema::dropIfExists('bookmarks');
    }
};