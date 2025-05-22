<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'service_type')) {
                $table->string('service_type')->default('in_shop')->after('category_id');
            }
            if (!Schema::hasColumn('services', 'travel_fee')) {
                $table->decimal('travel_fee', 10, 2)->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('services', 'service_radius')) {
                $table->integer('service_radius')->nullable()->after('travel_fee');
            }
            if (!Schema::hasColumn('services', 'travel_buffer_minutes')) {
                $table->integer('travel_buffer_minutes')->nullable()->after('service_radius');
            }
            if (!Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('travel_buffer_minutes');
            }
        });

        Schema::table('employee_service', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_service', 'travel_fee')) {
                $table->decimal('travel_fee', 10, 2)->nullable()->after('service_id');
            }
            if (!Schema::hasColumn('employee_service', 'service_radius')) {
                $table->integer('service_radius')->nullable()->after('travel_fee');
            }
            if (!Schema::hasColumn('employee_service', 'travel_buffer_minutes')) {
                $table->integer('travel_buffer_minutes')->nullable()->after('service_radius');
            }
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $columns = [
                'service_type',
                'travel_fee',
                'service_radius',
                'travel_buffer_minutes',
                'is_active'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('employee_service', function (Blueprint $table) {
            $columns = [
                'travel_fee',
                'service_radius',
                'travel_buffer_minutes'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_service', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 