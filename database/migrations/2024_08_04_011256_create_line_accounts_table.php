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
        Schema::table('line_accounts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('line_user_id')->unique();
            $table->string('line_user_name');
            $table->string('language')->nullable();
            $table->string('icon_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_accounts', function (Blueprint $table) {
            Schema::dropIfExists('line_accounts');
        });
    }
};
