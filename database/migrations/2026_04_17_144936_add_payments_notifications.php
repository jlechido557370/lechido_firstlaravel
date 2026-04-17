<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fine_amount and fine_paid to borrow_records if not present
        Schema::table('borrow_records', function (Blueprint $table) {
            if (!Schema::hasColumn('borrow_records', 'fine_amount')) {
                $table->decimal('fine_amount', 10, 2)->default(0)->after('returned_at');
            }
            if (!Schema::hasColumn('borrow_records', 'fine_paid')) {
                $table->boolean('fine_paid')->default(false)->after('fine_amount');
            }
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('borrow_record_id')->constrained('borrow_records')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('finverse_payment_id')->nullable();
            $table->string('finverse_link_id')->nullable();
            $table->text('payment_url')->nullable();
            $table->json('finverse_response')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // overdue, due_soon, payment_confirmed
            $table->text('message');
            $table->foreignId('borrow_record_id')->nullable()->constrained('borrow_records')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('payments');
        Schema::table('borrow_records', function (Blueprint $table) {
            $table->dropColumn(['fine_paid']);
        });
    }
};