<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_order_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('changed_by')->default('Administrator');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('pos_order_id')->references('id')->on('pos_orders')->onDelete('cascade');
            $table->index('pos_order_id');
        });

        Schema::create('table_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('pos_order_id')->nullable();
            $table->string('order_no')->nullable();
            $table->enum('status', ['Open','Closed'])->default('Open');
            $table->integer('covers')->default(1);
            $table->unsignedBigInteger('waiter_id')->nullable();
            $table->string('waiter_name')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('cascade');
            $table->index('table_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_sessions');
        Schema::dropIfExists('order_status_histories');
    }
};
