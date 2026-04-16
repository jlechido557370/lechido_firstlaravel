<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');         // e.g. 'book_borrowed', 'book_returned'
            $table->string('description');    // human-readable
            $table->json('meta')->nullable(); // extra data
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, fulfilled, cancelled
            $table->timestamps();
        });

        // Add fine_amount to borrow_records
        Schema::table('borrow_records', function (Blueprint $table) {
            $table->decimal('fine_amount', 8, 2)->default(0)->after('returned_at');
        });
    }

    public function down(): void
    {
        Schema::table('borrow_records', function (Blueprint $table) {
            $table->dropColumn('fine_amount');
        });
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('activity_logs');
    }
};