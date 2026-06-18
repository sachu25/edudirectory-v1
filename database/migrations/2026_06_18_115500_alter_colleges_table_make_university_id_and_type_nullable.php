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
        Schema::table('colleges', function (Blueprint $table) {
            // Check if foreign key exists to drop it safely
            $this->dropForeignKeyIfExists('colleges', 'colleges_university_id_foreign');
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->unsignedBigInteger('university_id')->nullable()->change();
            $table->string('type')->nullable()->change();
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $this->dropForeignKeyIfExists('colleges', 'colleges_university_id_foreign');
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->unsignedBigInteger('university_id')->nullable(false)->change();
            $table->enum('type', [
                'Autonomous', 
                'Affiliated', 
                'Deemed University', 
                'Private University', 
                'Government College', 
                'Aided College', 
                'Self Financing College'
            ])->nullable(false)->change();
        });

        Schema::table('colleges', function (Blueprint $table) {
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
        });
    }

    /**
     * Helper to drop foreign key constraint safely.
     */
    private function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }
    }
};
