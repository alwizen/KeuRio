<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Period (month & year)
            $table->unsignedTinyInteger('month');      // 1..12
            $table->unsignedSmallInteger('year');      // e.g. 2025

            // Money fields
            $table->decimal('base_salary',        15, 2)->default(0);
            $table->decimal('bonus',              15, 2)->default(0);
            $table->decimal('health_incentive',   15, 2)->default(0);
            $table->decimal('work_incentive',     15, 2)->default(0);
            $table->decimal('other',              15, 2)->default(0);   // miscellaneous
            $table->decimal('cash_advance',       15, 2)->default(0);   // cashbon

            $table->text('note')->nullable();
            $table->decimal('total_thp',          15, 2)->default(0);   // take home pay

            $table->timestamps();
            $table->softDeletes();

            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
