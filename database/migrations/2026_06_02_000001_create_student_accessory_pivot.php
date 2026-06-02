<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hostel_student_accessory')) {
            Schema::create('hostel_student_accessory', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->index();
                $table->unsignedBigInteger('accessory_id')->index();
                $table->timestamps();
                $table->unique(['student_id', 'accessory_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_student_accessory');
    }
};
