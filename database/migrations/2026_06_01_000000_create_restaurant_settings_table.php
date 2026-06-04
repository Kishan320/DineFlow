<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('business_unit');
            $table->text('restaurant_name');
            $table->string('gst_no');
            $table->text('bill_footer_text');
            $table->string('guest_bill')->default('Disabled');
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->index('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_settings');
    }
};
