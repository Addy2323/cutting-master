<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'is_at_home')) {
                $table->boolean('is_at_home')->default(false)->after('service_id');
            }
            if (!Schema::hasColumn('appointments', 'address')) {
                $table->string('address')->nullable()->after('is_at_home');
            }
            if (!Schema::hasColumn('appointments', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('appointments', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('city');
            }
            if (!Schema::hasColumn('appointments', 'travel_fee')) {
                $table->decimal('travel_fee', 10, 2)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('appointments', 'address_verified')) {
                $table->boolean('address_verified')->default(false)->after('travel_fee');
            }
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $columns = [
                'is_at_home',
                'address',
                'city',
                'postal_code',
                'travel_fee',
                'address_verified'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('appointments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 