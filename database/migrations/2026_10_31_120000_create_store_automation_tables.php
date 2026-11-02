<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token', 64)->unique();
            $table->json('cart_items');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('currency', 8)->default('PKR');
            $table->timestamp('last_activity_at')->index();
            $table->unsignedTinyInteger('reminders_sent')->default(0);
            $table->timestamp('last_reminder_at')->nullable();
            $table->foreignId('recovered_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('store_automation_dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('automation_type', 40);
            $table->string('reference_key', 120)->index();
            $table->unsignedTinyInteger('step_index')->default(0);
            $table->string('recipient')->nullable();
            $table->string('subject')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['automation_type', 'reference_key', 'step_index'], 'store_auto_dispatch_unique');
        });

        Schema::create('reorder_reminder_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_email')->index();
            $table->unsignedSmallInteger('cycle_days');
            $table->timestamp('due_at')->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'stock_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('stock_quantity')->nullable()->after('in_stock');
                $table->unsignedSmallInteger('reorder_cycle_days')->nullable()->after('stock_quantity');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reorder_reminder_schedules');
        Schema::dropIfExists('store_automation_dispatches');
        Schema::dropIfExists('abandoned_carts');

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['stock_quantity', 'reorder_cycle_days']);
            });
        }
    }
};
