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
        Schema::create('client_collections', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no')->unique();
            $table->string('month');
            $table->string('year');
            $table->date('date')->nullable();
            $table->decimal('total_bill_amount', 16, 0);
            $table->decimal('total_collection_amount', 16, 0);
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_collections');
    }
};
