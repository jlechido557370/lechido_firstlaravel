<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('hide_real_name')->default(false)->after('allow_dms');
            $table->boolean('is_subscribed')->default(false)->after('hide_real_name');
            $table->timestamp('subscription_expires_at')->nullable()->after('is_subscribed');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->json('genres')->nullable()->after('genre');
            $table->string('book_type', 20)->default('book')->after('genres');
            $table->unsignedInteger('manga_id')->nullable()->after('book_type');
        });

        Schema::table('user_books', function (Blueprint $table) {
            $table->json('genres')->nullable()->after('genre');
            $table->string('book_type', 20)->default('book')->after('genres');
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['blocker_id', 'blocked_id']);
            $table->timestamps();
        });

        DB::statement("UPDATE books SET genres = JSON_ARRAY(genre) WHERE genres IS NULL");
        DB::statement("UPDATE user_books SET genres = JSON_ARRAY(genre) WHERE genres IS NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
        Schema::table('user_books', function (Blueprint $table) { $table->dropColumn(['genres', 'book_type']); });
        Schema::table('books', function (Blueprint $table) { $table->dropColumn(['genres', 'book_type', 'manga_id']); });
        Schema::table('users', function (Blueprint $table) { $table->dropColumn(['hide_real_name', 'is_subscribed', 'subscription_expires_at']); });
    }
};