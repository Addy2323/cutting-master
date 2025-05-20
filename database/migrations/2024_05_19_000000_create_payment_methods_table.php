<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('credentials')->nullable();
            $table->timestamps();
        });

        // Insert default payment methods with online icons
        DB::table('payment_methods')->insert([
            [
                'name' => 'Vodacom M-Pesa',
                'code' => 'mpesa',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/1200px-M-PESA_LOGO-01.svg.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tigo Pesa',
                'code' => 'tigopesa',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Tigo_logo.svg/1200px-Tigo_logo.svg.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Halotel',
                'code' => 'halotel',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Halotel_logo.svg/1200px-Halotel_logo.svg.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Stripe_Logo%2C_revised_2016.svg/1200px-Stripe_Logo%2C_revised_2016.svg.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}; 