<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // The old `isbn` column has no default value, which breaks the seeder
            // since it now inserts via isbn_13 / isbn_10 instead.
            // Making it nullable gives it an implicit default of NULL.
            $table->string('isbn')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('isbn')->nullable(false)->change();
        });
    }
};