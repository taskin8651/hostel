<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hostel_complaints') && ! Schema::hasColumn('hostel_complaints', 'user_id')) {
            Schema::table('hostel_complaints', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->index()->after('category');
            });
        }

        if (Schema::hasTable('hostel_leaves') && ! Schema::hasColumn('hostel_leaves', 'user_id')) {
            Schema::table('hostel_leaves', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->index()->after('person_type');
            });
        }
    }

    public function down(): void
    {
        foreach (['hostel_complaints', 'hostel_leaves'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'user_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
