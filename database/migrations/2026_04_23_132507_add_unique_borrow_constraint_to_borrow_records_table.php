<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Partial unique indexes are supported by PostgreSQL and SQLite.
        // MySQL/MariaDB do not support them, so we rely on app-level locking there.
        if (in_array($driver, ['pgsql', 'sqlite'])) {
            DB::statement(
                'CREATE UNIQUE INDEX unique_active_borrow ON borrow_records (user_id, book_id) WHERE returned_at IS NULL'
            );
        } else {
            // Fallback: regular index for performance + app-level transaction locking
            Schema::table('borrow_records', function (Blueprint $table) {
                $table->index(['user_id', 'book_id', 'returned_at'], 'idx_borrow_user_book_returned');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['pgsql', 'sqlite'])) {
            DB::statement('DROP INDEX IF EXISTS unique_active_borrow');
        } else {
            Schema::table('borrow_records', function (Blueprint $table) {
                $table->dropIndex('idx_borrow_user_book_returned');
            });
        }
    }
};
