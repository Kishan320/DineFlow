<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_code')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('counter_name')->default('Main Counter');
            $table->string('branch_name')->default('Main Branch');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('closing_balance', 12, 2)->nullable();
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_cash', 12, 2)->default(0);
            $table->decimal('total_card', 12, 2)->default(0);
            $table->decimal('total_upi', 12, 2)->default(0);
            $table->decimal('total_others', 12, 2)->default(0);
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->index('session_code');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};
