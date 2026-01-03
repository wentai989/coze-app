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
        Schema::create('codes', function (Blueprint $table) {
            $table->comment('算力套餐表');
            $table->increments('id');
            $table->integer('mch_id')->nullable();
            $table->string('name')->comment('生成名称');
            $table->integer('value')->comment('金额');
            $table->string('code', 50)->nullable()->comment('卡密');
            $table->boolean('code_type')->nullable()->default(true)->comment('类型1算力，2会员');
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->integer('user_id')->nullable();
            $table->timestamp('invoke_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
