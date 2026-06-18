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
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->enum('type', [
                'Autonomous', 
                'Affiliated', 
                'Deemed University', 
                'Private University', 
                'Government College', 
                'Aided College', 
                'Self Financing College'
            ]);
            $table->string('naac_grade')->nullable();
            $table->integer('nirf_ranking')->nullable();
            $table->year('established_year')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('pin_code')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('office_mobile')->nullable();
            $table->string('official_email')->nullable();
            $table->integer('student_strength')->nullable();
            $table->integer('faculty_strength')->nullable();
            $table->text('courses_offered')->nullable();
            $table->boolean('hostel_facility')->default(false);
            $table->boolean('placement_cell')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
