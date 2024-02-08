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
        Schema::create('conversion_factors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_asset_id');
            $table->unsignedBigInteger('to_asset_id');
            $table->foreign('from_asset_id')->references('id')->on('assets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('to_asset_id')->references('id')->on('assets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('fee', 4, 2)->comment('fee percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_factors');
    }
};
