<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('books', 'google_books_id')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('google_books_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('books', 'google_books_id')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('google_books_id');
            });
        }
    }
};