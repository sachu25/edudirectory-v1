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
        Schema::create('non_academic_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            // Primary Contact Person Details
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_designation')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('contact_person_phone')->nullable();
            
            // Contacted Employee detail & reason
            $table->foreignId('contacted_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('contact_reason')->nullable();
            $table->date('contact_date')->nullable();
            $table->string('contact_mode')->nullable();
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
        Schema::dropIfExists('non_academic_clients');
    }
};
