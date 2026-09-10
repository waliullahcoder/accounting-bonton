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
        Schema::create('client_collection_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_collection_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->string('month');
            $table->string('year');
            $table->decimal('bill_amount', 16, 0);
            $table->decimal('collection_amount', 16, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_collection_lists');
    }
};
