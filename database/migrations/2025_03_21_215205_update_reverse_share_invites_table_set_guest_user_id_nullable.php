<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reverse_share_invites', function (Blueprint $table) {
            // Drop the foreign key constraint first if it exists
            $table->dropForeign(['guest_user_id']);
            
            // Modify the column to be nullable
            $table->foreignId('guest_user_id')->nullable()->change();
            
            // Add the foreign key constraint back
            $table->foreign('guest_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reverse_share_invites', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['guest_user_id']);
            
            // Make the column non-nullable again
            $table->foreignId('guest_user_id')->nullable(false)->change();
            
            // Add the foreign key constraint back
            $table->foreign('guest_user_id')->references('id')->on('users');
        });
    }
};
