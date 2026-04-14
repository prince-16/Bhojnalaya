<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tables') && ! Schema::hasTable('dining_tables')) {
            Schema::rename('tables', 'dining_tables');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('dining_tables') && ! Schema::hasTable('tables')) {
            Schema::rename('dining_tables', 'tables');
        }
    }
};
