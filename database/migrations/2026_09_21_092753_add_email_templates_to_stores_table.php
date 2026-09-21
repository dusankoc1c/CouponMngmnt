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
        Schema::table('stores', function (Blueprint $table) {
            $table->text('initial_mail_template')->nullable();
            $table->text('reminder_mail_template')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            Schema::table('stores', function (Blueprint $table) {
                $table->dropColumn(['initial_email_template', 'reminder_email_template']);
            });
        });
    }
};
