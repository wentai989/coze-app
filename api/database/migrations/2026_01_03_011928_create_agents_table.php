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
        Schema::create('agents', function (Blueprint $table) {
            $table->comment('智能体配置表');
            $table->increments('id');
            $table->string('name')->comment('智能体名称');
            $table->string('icon')->nullable()->comment('图标URL');
            $table->text('introduction')->nullable()->comment('介绍');
            $table->text('opening_remark')->nullable()->comment('开场白');
            $table->text('prompt_settings')->nullable()->comment('提示词设置');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->boolean('is_status')->nullable()->default(true)->comment('状态');
            $table->integer('sort')->nullable()->default(0)->comment('排序');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
