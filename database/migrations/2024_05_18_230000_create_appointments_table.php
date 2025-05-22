<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('booking_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('notes')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('booking_date');
            $table->string('booking_time');
            $table->enum('status', [
                'Pending payment',
                'Processing',
                'Confirmed',
                'Cancelled',
                'Completed',
                'On Hold',
                'Rescheduled',
                'No Show'
            ])->default('Pending payment');
            $table->json('other')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}; 