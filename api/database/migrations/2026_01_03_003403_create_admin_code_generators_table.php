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
        Schema::create('admin_code_generators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->string('table_name')->default('');
            $table->string('primary_key')->default('id');
            $table->string('model_name')->default('');
            $table->string('controller_name')->default('');
            $table->string('service_name')->default('');
            $table->text('columns');
            $table->integer('need_timestamps')->default(0);
            $table->integer('soft_delete')->default(0);
            $table->text('needs')->nullable();
            $table->text('menu_info')->nullable();
            $table->text('page_info')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_code_generators');
    }
};
