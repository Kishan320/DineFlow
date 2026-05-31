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
            $table->timestamps();
            $table->index('waiter_code');
            $table->index('name');
            $table->index('mobile');
            $table->index('dob');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiters');
    }
};
