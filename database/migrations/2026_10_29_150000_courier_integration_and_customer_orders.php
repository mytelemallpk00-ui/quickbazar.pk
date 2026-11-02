<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_api_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('platform', 32)->unique();
            $table->text('credentials')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('shipping_method_id', 64)->nullable()->after('shipping_country');
            $table->string('courier_code', 32)->nullable()->after('shipping_method_id');
            $table->string('courier_label', 120)->nullable()->after('courier_code');
            $table->string('tracking_number', 120)->nullable()->after('courier_label');
            $table->string('courier_booking_ref', 120)->nullable()->after('tracking_number');
            $table->string('courier_booking_status', 32)->default('pending')->after('courier_booking_ref');
            $table->text('courier_error')->nullable()->after('courier_booking_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'shipping_method_id',
                'courier_code',
                'courier_label',
                'tracking_number',
                'courier_booking_ref',
                'courier_booking_status',
                'courier_error',
            ]);
        });

        Schema::dropIfExists('courier_api_settings');
    }
};
