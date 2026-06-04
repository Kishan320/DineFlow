<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_order_id');
            $table->unsignedBigInteger('item_id');
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->string('category')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_type')->default('flat');
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->string('tax_type')->default('Exclusive');
            $table->decimal('line_total', 12, 2);
            $table->text('notes')->nullable();
            $table->boolean('kot_printed')->default(false);
            $table->timestamps();
            $table->foreign('pos_order_id')->references('id')->on('pos_orders')->onDelete('cascade');
            $table->index('pos_order_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_order_items');
    }
};
