<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add the new column
        Schema::table('contact_persons', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->after('college_id')->constrained('designations')->onDelete('set null');
        });

        // Migrate existing data (if any)
        $contacts = DB::table('contact_persons')->get();
        foreach ($contacts as $contact) {
            if (!empty($contact->designation)) {
                $designationId = DB::table('designations')->insertGetId([
                    'name' => $contact->designation,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('contact_persons')->where('id', $contact->id)->update(['designation_id' => $designationId]);
            }
        }

        // Drop the old string column
        Schema::table('contact_persons', function (Blueprint $table) {
            $table->dropColumn('designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_persons', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('designation_id');
        });

        $contacts = DB::table('contact_persons')->get();
        foreach ($contacts as $contact) {
            if (!empty($contact->designation_id)) {
                $designation = DB::table('designations')->where('id', $contact->designation_id)->first();
                if ($designation) {
                    DB::table('contact_persons')->where('id', $contact->id)->update(['designation' => $designation->name]);
                }
            }
        }

        Schema::table('contact_persons', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropColumn('designation_id');
        });
    }
};
