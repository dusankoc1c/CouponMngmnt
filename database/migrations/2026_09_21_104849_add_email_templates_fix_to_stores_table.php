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
            $table->renameColumn('initial_mail_template', 'initial_email_template');
            $table->renameColumn('reminder_mail_template', 'reminder_email_template');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('initial_email_template', 'initial_mail_template');
            $table->renameColumn('reminder_email_template', 'reminder_mail_template');
        });
    }
};
