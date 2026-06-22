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
        Schema::table('colleges', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->after('id')->constrained('universities')->onDelete('set null');
        });

        // Migrate existing plain text data to universities table
        $colleges = DB::table('colleges')->get();
        foreach ($colleges as $college) {
            if (!empty($college->affiliated_university)) {
                $uniName = trim($college->affiliated_university);
                
                // Get or create university
                $universityId = DB::table('universities')->where('name', $uniName)->whereNull('deleted_at')->value('id');
                if (!$universityId) {
                    $universityId = DB::table('universities')->insertGetId([
                        'name' => $uniName,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                DB::table('colleges')->where('id', $college->id)->update([
                    'university_id' => $universityId,
                ]);
            }
        }

        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn('affiliated_university');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->string('affiliated_university')->nullable()->after('type');
        });

        // Migrate data back
        $colleges = DB::table('colleges')->get();
        foreach ($colleges as $college) {
            if ($college->university_id) {
                $uniName = DB::table('universities')->where('id', $college->university_id)->value('name');
                DB::table('colleges')->where('id', $college->id)->update([
                    'affiliated_university' => $uniName,
                ]);
            }
        }

        Schema::table('colleges', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropColumn('university_id');
        });
    }
};
