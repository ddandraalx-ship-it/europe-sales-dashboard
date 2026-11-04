<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_transactions')) {
            return;
        }

        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('region', 80)->index();
            $table->string('country', 120)->index();
            $table->string('item_type', 80)->index();
            $table->string('sales_channel', 20)->index();
            $table->char('order_priority', 1);
            $table->date('order_date')->index();
            $table->bigInteger('order_id')->unique();
            $table->date('ship_date');
            $table->unsignedInteger('units_sold');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_revenue', 18, 2);
            $table->decimal('total_cost', 18, 2);
            $table->decimal('total_profit', 18, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Keep sales data intact; it may have existed before this migration.
    }
};
