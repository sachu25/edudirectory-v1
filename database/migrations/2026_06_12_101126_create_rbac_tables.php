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
        // 1. Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('module');
            $table->timestamps();
        });

        // 3. Create role_permission pivot table
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Seed default roles into the new table so we can map existing users
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'label' => 'Administrator',
            'description' => 'System administrator with full control',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staffRoleId = DB::table('roles')->insertGetId([
            'name' => 'staff',
            'label' => 'Staff Member',
            'description' => 'Staff member who manages data and interactions',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userRoleId = DB::table('roles')->insertGetId([
            'name' => 'user',
            'label' => 'Regular User',
            'description' => 'Regular user with view-only permissions',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Alter users table to add role_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->onDelete('set null');
        });

        // 6. Map existing users from the old 'role' enum column to 'role_id'
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $targetRoleId = $staffRoleId; // default fallback
            if ($user->role === 'admin') {
                $targetRoleId = $adminRoleId;
            } elseif ($user->role === 'staff') {
                $targetRoleId = $staffRoleId;
            }
            
            DB::table('users')->where('id', $user->id)->update([
                'role_id' => $targetRoleId
            ]);
        }

        // 7. Drop the old enum column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add role column to users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff'])->default('staff')->after('role_id');
        });

        // Map role_id back to enum value
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $role = DB::table('roles')->where('id', $user->role_id)->first();
            $enumVal = 'staff';
            if ($role && $role->name === 'admin') {
                $enumVal = 'admin';
            }
            DB::table('users')->where('id', $user->id)->update([
                'role' => $enumVal
            ]);
        }

        // Drop dynamic role relations
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
