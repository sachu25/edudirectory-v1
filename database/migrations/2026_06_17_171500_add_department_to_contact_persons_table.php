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
        if (!Schema::hasColumn('contact_persons', 'department')) {
            Schema::table('contact_persons', function (Blueprint $table) {
                $table->string('department')->nullable()->after('designation_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('contact_persons', 'department')) {
            Schema::table('contact_persons', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }
};
