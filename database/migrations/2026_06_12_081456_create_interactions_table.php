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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained('colleges')->onDelete('cascade');
            $table->foreignId('contact_person_id')->nullable()->constrained('contact_persons')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('interaction_status_id')->nullable()->constrained('interaction_statuses')->onDelete('set null');
            $table->datetime('contact_date');
            $table->string('contact_mode'); // Call, Email, WhatsApp, Meeting
            $table->text('college_response')->nullable();
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
        Schema::dropIfExists('interactions');
    }
};
