<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('order_no')->unique();
            $table->string('invoice_no')->unique()->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('table_label')->nullable();
            $table->unsignedBigInteger('waiter_id')->nullable();
            $table->string('waiter_name')->nullable();
            $table->enum('order_type', ['Dine In', 'Takeaway', 'Delivery'])->default('Dine In');
            $table->unsignedInteger('covers')->default(1);
            $table->text('notes')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->text('delivery_notes')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->json('tax_breakdown')->nullable();
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('discount_type')->default('flat');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('round_off', 10, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);
            $table->enum('status', ['Pending','Confirmed','Preparing','Ready','Served','Completed','Cancelled'])->default('Pending');
            $table->enum('payment_status', ['Unpaid','Partial','Paid'])->default('Unpaid');
            $table->string('bill_pay_type')->default('Cash');
            $table->decimal('cash_amt', 12, 2)->default(0);
            $table->string('card_ref')->nullable();
            $table->decimal('card_amt', 12, 2)->default(0);
            $table->string('others_type')->nullable();
            $table->string('others_ref')->nullable();
            $table->decimal('others_amt', 12, 2)->default(0);
            $table->decimal('upi_amt', 12, 2)->default(0);
            $table->string('upi_ref')->nullable();
            $table->text('payment_note')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->index('order_no');
            $table->index('invoice_no');
            $table->index('session_id');
            $table->index('customer_id');
            $table->index('table_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_type');
            $table->index('created_at');
            $table->index('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
