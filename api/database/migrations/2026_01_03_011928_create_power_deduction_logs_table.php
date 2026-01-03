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
        Schema::create('power_deduction_logs', function (Blueprint $table) {
            $table->comment('算力扣点记录表');
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户ID (假设存在 users 表)');
            $table->string('name')->comment('扣点名称');
            $table->decimal('power_value', 10)->comment('扣除算力值');
            $table->string('deduction_type')->comment('扣点类型');
            $table->string('chat_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->integer('mch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_deduction_logs');
    }
};
