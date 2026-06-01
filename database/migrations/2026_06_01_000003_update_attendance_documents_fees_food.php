<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hostel_student_attendance')) {
            Schema::table('hostel_student_attendance', function (Blueprint $table) {
                if (! Schema::hasColumn('hostel_student_attendance', 'movement_type')) {
                    $table->string('movement_type')->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('hostel_fee_payments')) {
            Schema::table('hostel_fee_payments', function (Blueprint $table) {
                if (! Schema::hasColumn('hostel_fee_payments', 'attachment')) {
                    $table->string('attachment')->nullable()->after('payment_mode');
                }
            });
        }

        if (! Schema::hasTable('hostel_documents')) {
            Schema::create('hostel_documents', function (Blueprint $table) {
                $table->id();
                $table->string('person_type')->nullable()->default('student');
                $table->unsignedBigInteger('student_id')->nullable()->index();
                $table->unsignedBigInteger('staff_id')->nullable()->index();
                $table->string('document_type')->nullable()->default('other');
                $table->string('document_name')->nullable();
                $table->string('document_file')->nullable();
                $table->text('remark')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_documents');

        if (Schema::hasTable('hostel_fee_payments') && Schema::hasColumn('hostel_fee_payments', 'attachment')) {
            Schema::table('hostel_fee_payments', function (Blueprint $table) {
                $table->dropColumn('attachment');
            });
        }

        if (Schema::hasTable('hostel_student_attendance') && Schema::hasColumn('hostel_student_attendance', 'movement_type')) {
            Schema::table('hostel_student_attendance', function (Blueprint $table) {
                $table->dropColumn('movement_type');
            });
        }
    }
};
