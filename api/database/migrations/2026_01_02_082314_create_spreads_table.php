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
        Schema::create('spreads', function (Blueprint $table) {
            $table->comment('算力扣点记录表');
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('推广员ID');
            $table->string('tg_user_id')->nullable()->comment('推广人');
            $table->decimal('amount', 10)->default(0)->comment('分成');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('spread_type')->nullable()->default(true)->comment('1到账，2提现');
            $table->boolean('is_status')->nullable()->default(false)->comment('0正在处理，1完成，2失败');
            $table->string('remark', 50)->nullable();
            $table->integer('mch_id');
            $table->string('amount_img')->nullable();
            $table->string('audit_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spreads');
    }
};
