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
        Schema::create('konts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name')->comment('空间名称');
            $table->string('space_id')->comment('扣子空间ID');
            $table->string('app_id')->comment('appid');
            $table->string('app_secret')->comment('公钥');
            $table->text('app_key')->comment('私钥证书');
            $table->integer('mch_id');
            $table->integer('is_status')->nullable()->default(1);
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('更新时间');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konts');
    }
};
