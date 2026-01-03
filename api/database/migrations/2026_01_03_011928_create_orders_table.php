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
        Schema::create('orders', function (Blueprint $table) {
            $table->comment('订单记录表');
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户ID (假设存在 users 表)');
            $table->string('name')->comment('充值名称');
            $table->decimal('amount', 10)->comment('充值金额');
            $table->string('order_no')->unique('order_number_unique')->comment('订单号');
            $table->boolean('invoiced')->default(false)->comment('是否开票');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('order_type')->default(true)->comment('1是算力订单，2会员订单');
            $table->boolean('is_status')->nullable()->default(false);
            $table->decimal('power_value', 10)->nullable()->default(0)->comment('充值算力');
            $table->integer('day_number')->nullable()->comment('充值天数');
            $table->integer('mch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
