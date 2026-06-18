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
        Schema::create('contact_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert default contact modes
        $modes = ['Call', 'Email', 'WhatsApp', 'In-Person Meeting'];
        $now = now();
        foreach ($modes as $mode) {
            DB::table('contact_modes')->insert([
                'name' => $mode,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // Add contact_mode_id column to interactions table
        Schema::table('interactions', function (Blueprint $table) {
            $table->foreignId('contact_mode_id')->nullable()->after('interaction_status_id')->constrained('contact_modes')->onDelete('set null');
        });

        // Map existing string contact_mode values to the new contact_modes ids
        $interactions = DB::table('interactions')->get();
        foreach ($interactions as $i) {
            $matchingMode = 'Call';
            if (isset($i->contact_mode)) {
                if (stripos($i->contact_mode, 'email') !== false) {
                    $matchingMode = 'Email';
                } elseif (stripos($i->contact_mode, 'whatsapp') !== false) {
                    $matchingMode = 'WhatsApp';
                } elseif (stripos($i->contact_mode, 'meeting') !== false || stripos($i->contact_mode, 'in-person') !== false) {
                    $matchingMode = 'In-Person Meeting';
                }
            }
            
            $modeId = DB::table('contact_modes')->where('name', $matchingMode)->value('id');
            if ($modeId) {
                DB::table('interactions')->where('id', $i->id)->update(['contact_mode_id' => $modeId]);
            }
        }

        // Drop the legacy contact_mode string column
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropColumn('contact_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back contact_mode column to interactions table
        Schema::table('interactions', function (Blueprint $table) {
            $table->string('contact_mode')->nullable()->after('contact_date');
        });

        // Populate it back
        $interactions = DB::table('interactions')->get();
        foreach ($interactions as $i) {
            if ($i->contact_mode_id) {
                $modeName = DB::table('contact_modes')->where('id', $i->contact_mode_id)->value('name');
                if ($modeName) {
                    DB::table('interactions')->where('id', $i->id)->update(['contact_mode' => $modeName]);
                }
            }
        }

        Schema::table('interactions', function (Blueprint $table) {
            $table->dropForeign(['contact_mode_id']);
            $table->dropColumn('contact_mode_id');
        });

        Schema::dropIfExists('contact_modes');
    }
};
