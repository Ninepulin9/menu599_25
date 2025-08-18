<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('total');
            }
            
            if (!Schema::hasColumn('orders', 'is_print_cook')) {
                $table->boolean('is_print_cook')->default(0)->after('status');
            }
            
            if (!Schema::hasColumn('orders', 'is_pay')) {
                $table->boolean('is_pay')->default(0)->after('is_print_cook');
            }
            
            if (!Schema::hasColumn('orders', 'is_type')) {
                $table->integer('is_type')->nullable()->after('is_pay');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount', 
                'is_print_cook', 
                'is_pay', 
                'is_type'
            ]);
        });
    }
};