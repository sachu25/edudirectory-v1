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
        Schema::table('universities', function (Blueprint $table) {
            $table->dropUnique('universities_name_unique');
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->dropUnique('colleges_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->unique('name', 'universities_name_unique');
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->unique('code', 'colleges_code_unique');
        });
    }
};
