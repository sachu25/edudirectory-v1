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
        Schema::disableForeignKeyConstraints();

        // 1. Drop foreign key and column from colleges
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
            
            // 2. Add new columns for Option B
            $table->boolean('is_university')->default(false)->after('name');
            $table->string('affiliated_university')->nullable()->after('type');
        });

        // 3. Drop universities table
        Schema::dropIfExists('universities');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Recreate universities table
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Re-add university_id to colleges
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn(['is_university', 'affiliated_university']);
            $table->foreignId('university_id')->nullable()->constrained()->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }
};
