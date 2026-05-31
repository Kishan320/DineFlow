<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('hsn_code')->unique();
            $table->string('description');
            $table->decimal('cgst', 8, 2)->default(0);
            $table->decimal('sgst', 8, 2)->default(0);
            $table->decimal('igst', 8, 2)->default(0);
            $table->decimal('cess', 8, 2)->default(0);
            $table->decimal('additional_cess', 8, 2)->default(0);
            $table->decimal('vat', 8, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->string('last_accessed_by')->default('Administrator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
