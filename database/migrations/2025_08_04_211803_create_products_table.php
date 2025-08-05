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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['peso', 'pezzo']);
            $table->string('unit')->default('kg');
            $table->foreignId('category_id')->nullable()->constrained();
            $table->decimal('sale_price', 8, 2);
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->decimal('stock_quantity', 8, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
