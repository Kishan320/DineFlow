<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('hsn_code');
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
            $table->unique(['created_by', 'hsn_code']);
            $table->index('created_by');
            $table->index('description');
            $table->index(['cgst', 'sgst', 'igst']);
            $table->index('tax_percent');
            $table->index('last_accessed_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
