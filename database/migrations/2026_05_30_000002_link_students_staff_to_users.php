<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
        });

        Schema::table('hostel_staff', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hostel_staff', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('hostel_students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
