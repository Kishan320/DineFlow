<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique();
            $table->string('description')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->index('category_name');
            $table->index('description');
            $table->index('last_accessed_by');
        });     
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
