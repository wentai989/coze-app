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
        Schema::create('app_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('mch_id')->comment('商户iD');
            $table->integer('app_id')->nullable()->index('pid');
            $table->integer('user_id')->nullable()->index('user_id');
            $table->mediumText('contents')->nullable();
            $table->string('log_type', 50)->nullable()->index('log_type');
            $table->boolean('is_status')->nullable()->default(true);
            $table->string('execute_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('更新时间');
            $table->text('outputs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_logs');
    }
};
