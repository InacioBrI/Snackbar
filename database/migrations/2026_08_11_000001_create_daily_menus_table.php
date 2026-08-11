<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_menus', function (Blueprint $table) {
            $table->id();
            $table->date('menu_date')->unique();
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('daily_menu_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_sold_out')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['daily_menu_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_menu_product');
        Schema::dropIfExists('daily_menus');
    }
};
