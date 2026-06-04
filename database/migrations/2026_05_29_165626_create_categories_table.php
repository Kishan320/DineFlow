<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('category_name');
            $table->string('description')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->unique(['created_by', 'category_name']);
            $table->index('created_by');
            $table->index('description');
            $table->index('last_accessed_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });     
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
