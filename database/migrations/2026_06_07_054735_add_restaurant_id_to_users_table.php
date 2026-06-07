<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // restaurant_id groups all staff under the same restaurant account.
            // Owner/super-admin has restaurant_id = their own user ID (set by seeder).
            // Staff members get restaurant_id of their admin/owner.
            $table->unsignedBigInteger('restaurant_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('restaurant_id');
        });
    }
};
