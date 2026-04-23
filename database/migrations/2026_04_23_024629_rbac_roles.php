<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add RBAC support: ensures the users.role column accepts
     * the four roles: user, subscribed_user, staff, admin.
     *
     * No structural change needed since role is already a string column.
     * This migration documents the change and sets the correct default.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure role column has the correct default (re-apply in case it differs)
            $table->string('role')->default('user')->change();
        });
    }

    public function down(): void
    {
        // Nothing to undo — role column already existed
    }
};