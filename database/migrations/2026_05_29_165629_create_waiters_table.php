<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('waiters', function (Blueprint $table) {
            $table->id();
            $table->string('waiter_code')->unique();
            $table->string('name');
            $table->string('mobile');
            $table->date('dob')->nullable();
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
            $table->index('waiter_code');
            $table->index('name');
            $table->index('mobile');
            $table->index('dob');
            $table->index('last_accessed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiters');
    }
};
