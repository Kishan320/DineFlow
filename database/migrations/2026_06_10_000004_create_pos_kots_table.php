<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_kots', function (Blueprint $table) {
            $table->id();
            $table->string('kot_no')->unique();
            $table->unsignedBigInteger('pos_order_id');
            $table->string('order_no');
            $table->string('table_label')->nullable();
            $table->string('order_type')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('waiter_name')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending','Preparing','Ready','Served'])->default('Pending');
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->foreign('pos_order_id')->references('id')->on('pos_orders')->onDelete('cascade');
            $table->index('pos_order_id');
            $table->index('kot_no');
            $table->index('status');
        });

        Schema::create('pos_kot_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_kot_id');
            $table->unsignedBigInteger('item_id');
            $table->string('item_name');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('pos_kot_id')->references('id')->on('pos_kots')->onDelete('cascade');
            $table->index('pos_kot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_kot_items');
        Schema::dropIfExists('pos_kots');
    }
};
