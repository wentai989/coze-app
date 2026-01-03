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
        Schema::create('mchs', function (Blueprint $table) {
            $table->comment('商户');
            $table->integer('id', true);
            $table->string('name', 50)->nullable()->comment('名称');
            $table->string('logo')->nullable()->comment('logo');
            $table->string('service_at')->nullable()->comment('服务到期时间');
            $table->boolean('is_h5')->nullable()->default(false)->comment('开启h5');
            $table->boolean('is_mini')->nullable()->default(false)->comment('开启小程序');
            $table->boolean('is_pc')->nullable()->default(false)->comment('开启pc');
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('remark')->nullable()->comment('备注');
            $table->text('mini_config')->nullable();
            $table->string('app_key', 50)->nullable()->index('app_key')->comment('系统key');
            $table->text('power')->nullable()->comment('算力赠送设置');
            $table->text('mini')->nullable()->comment('小程序设置');
            $table->text('pay')->nullable()->comment('微信支付配置');
            $table->string('contact_qrcode')->nullable()->comment('客服图片');
            $table->text('share')->nullable();
            $table->decimal('spread_value', 10)->nullable()->default(0)->comment('返佣比例');
            $table->boolean('is_spread')->nullable()->default(false)->comment('是否开启返佣');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mchs');
    }
};
