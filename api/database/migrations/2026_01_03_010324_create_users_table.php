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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->rememberToken();
            $table->timestamps();
            $table->string('phone', 11)->nullable();
            $table->string('openid')->nullable();
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->decimal('power_value', 11)->nullable()->default(0)->comment('剩余算力');
            $table->string('sign_at', 15)->nullable()->comment('签到时间');
            $table->date('vip_expire_time')->nullable()->comment('会员过期时间');
            $table->integer('mch_id')->nullable()->comment('商户ID');
            $table->integer('invite_count')->nullable()->default(0)->comment('推广数量');
            $table->integer('ask_id')->nullable()->comment('推广员');
            $table->decimal('amount', 10)->nullable()->default(0)->comment('提成金额');
            $table->string('amount_img')->nullable()->comment('收款码');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
