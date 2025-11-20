<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('product_id');
            $table->uuid('source_store_id')->nullable();
            $table->uuid('target_store_id')->nullable();

            $table->integer('quantity');

            $table->enum('type', ['IN', 'OUT', 'TRANSFER']);

            $table->timestamp('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('source_store_id')->references('id')->on('stores');
            $table->foreign('target_store_id')->references('id')->on('stores');

            //indices
            $table->index(['product_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
