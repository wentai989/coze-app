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
        Schema::create('apps', function (Blueprint $table) {
            $table->comment('AI 应用表');
            $table->increments('id');
            $table->string('name')->comment('应用名称');
            $table->text('description')->nullable()->comment('应用描述');
            $table->unsignedInteger('categorie_id')->nullable()->index('category_id')->comment('分类');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->string('image')->nullable()->comment('图片');
            $table->integer('used_num')->nullable()->default(0)->comment('已使用人数');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->decimal('use_power', 10)->nullable()->comment('消耗算力');
            $table->integer('mch_id');
            $table->text('launch_params')->nullable()->comment('启动参数');
            $table->text('output_params')->nullable()->comment('输出参数');
            $table->boolean('app_type')->nullable()->default(true)->comment('类型');
            $table->boolean('power_type')->nullable()->default(true);
            $table->decimal('multiple_power', 10)->nullable()->default(1)->comment('扣点倍数');
            $table->string('bot_id')->nullable();
            $table->dateTime('publish_time')->nullable();
            $table->integer('kont_id')->nullable();
            $table->text('configs')->nullable();
            $table->boolean('is_vip')->nullable()->default(false);
            $table->boolean('icon_change')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
