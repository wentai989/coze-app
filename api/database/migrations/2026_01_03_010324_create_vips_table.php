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
        Schema::create('vips', function (Blueprint $table) {
            $table->comment('算力套餐表');
            $table->increments('id');
            $table->string('name')->comment('套餐名称');
            $table->decimal('amount', 10)->comment('金额');
            $table->text('description')->nullable()->comment('会员介绍');
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedInteger('power_value')->comment('赠送算力值');
            $table->integer('day_number')->comment('有效天数');
            $table->integer('mch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vips');
    }
};
