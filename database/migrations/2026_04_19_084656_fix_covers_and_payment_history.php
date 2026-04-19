<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add type + subscription fields to payments ────────────────────────
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'type')) {
                $table->string('type', 20)->default('fine')->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'subscription_plan')) {
                $table->string('subscription_plan', 20)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_plan');
            }
        });

        // ── Fix manga/comic covers: clear bad OpenLibrary URLs ────────────────
        // Book::coverUrl() will fall back to Google Books by ISBN which is reliable
        DB::table('books')
            ->whereIn('book_type', ['manga', 'comic'])
            ->where('cover_image', 'like', 'https://covers.openlibrary.org%')
            ->update(['cover_image' => null]);

        // Also fix any regular books that may have bad OpenLibrary covers
        // by clearing cover_image so Google Books ISBN fallback kicks in
        // (only for books where isbn_13 exists)
        DB::table('books')
            ->where('book_type', 'book')
            ->where('cover_image', 'like', 'https://covers.openlibrary.org%')
            ->whereNotNull('isbn_13')
            ->update(['cover_image' => null]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['type', 'subscription_plan', 'subscription_expires_at'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};