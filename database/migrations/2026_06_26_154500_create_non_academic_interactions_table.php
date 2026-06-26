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
        Schema::create('non_academic_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('non_academic_client_id')->constrained('non_academic_clients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('contact_date');
            $table->string('contact_mode'); // Email, Phone Call, In-Person Visit, LinkedIn, WhatsApp
            $table->string('interaction_status'); // Interested, Not Interested, Follow-up, Meeting Scheduled, MoU Signed, Closed
            $table->string('purpose'); // Campus Placement, Internship, MoU, Consultancy, Industrial Visit, etc.
            $table->text('client_response')->nullable();
            $table->text('remarks')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_academic_interactions');
    }
};
