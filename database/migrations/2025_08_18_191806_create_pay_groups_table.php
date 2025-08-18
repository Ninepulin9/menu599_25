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
        if (!Schema::hasTable('pay_groups')) {
            Schema::create('pay_groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pay_id');
                $table->unsignedBigInteger('order_id');
                $table->timestamps();

                if (Schema::hasTable('pays')) {
                    $table->foreign('pay_id')->references('id')->on('pays')->onDelete('cascade');
                }
                
                if (Schema::hasTable('orders')) {
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                }
                
                
                $table->unique(['pay_id', 'order_id']);
                
                $table->index('pay_id');
                $table->index('order_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_groups');
    }
};