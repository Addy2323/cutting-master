<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('calendar_id')->nullable()->after('other');
            $table->string('calendar_provider')->nullable()->after('calendar_id');
            $table->json('availability')->nullable()->after('calendar_provider');
            $table->boolean('auto_sync_calendar')->default(false)->after('availability');
            $table->string('timezone')->default('UTC')->after('auto_sync_calendar');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'calendar_id',
                'calendar_provider',
                'availability',
                'auto_sync_calendar',
                'timezone'
            ]);
        });
    }
}; 