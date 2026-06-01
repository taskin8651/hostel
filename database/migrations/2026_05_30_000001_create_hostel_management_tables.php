<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (config('hostel.modules') as $module) {
            Schema::create($module['table'], function (Blueprint $table) use ($module) {
                $table->id();

                foreach ($module['fields'] as $name => $field) {
                    $nullable = ($field['nullable'] ?? false) || ! ($field['required'] ?? false);

                    match ($field['type']) {
                        'number' => $table->decimal($name, 12, 2)->default($field['default'] ?? 0),
                        'date' => $table->date($name)->nullable($nullable),
                        'time' => $table->time($name)->nullable($nullable),
                        'textarea' => $table->text($name)->nullable($nullable),
                        'file' => $table->string($name)->nullable(),
                        default => $table->string($name)->nullable($nullable),
                    };
                }

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse(config('hostel.modules')) as $module) {
            Schema::dropIfExists($module['table']);
        }
    }
};
