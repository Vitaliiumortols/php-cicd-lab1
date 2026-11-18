<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('model_name');
            $table->string('color');
            $table->unsignedSmallInteger('storage_gb');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('price_kop');
            $table->unsignedInteger('stock_qty')->default(0);
            $table->unsignedInteger('reserved_qty')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['active', 'price_kop']);
            $table->index(['model_name', 'storage_gb']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
