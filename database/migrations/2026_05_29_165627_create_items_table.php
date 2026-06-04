<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('code');
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->decimal('restaurant_price', 10, 2)->default(0);
            $table->decimal('bar_price', 10, 2)->default(0);
            $table->decimal('room_price', 10, 2)->default(0);
            $table->enum('tax_type', ['Exclusive', 'Inclusive'])->default('Exclusive');
            $table->string('tax')->nullable();
            $table->enum('state', ['On Sale', 'Off Sale'])->default('On Sale');
            $table->enum('item_type', ['Physical Item', 'Digital Item', 'Service'])->default('Physical Item');
            $table->text('note')->nullable();
            $table->string('image_url')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->unique(['created_by', 'code']);
            $table->index('created_by');
            $table->index('item_type');
            $table->index('category');
            $table->index('restaurant_price');
            $table->index('bar_price');
            $table->index('room_price');
            $table->index(['created_at', 'id']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
