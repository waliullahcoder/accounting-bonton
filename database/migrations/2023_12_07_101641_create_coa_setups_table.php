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
        Schema::create('coa_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable();
            $table->bigInteger('head_code');
            $table->string('head_name');
            $table->tinyInteger('transaction')->default(0);
            $table->tinyInteger('general')->default(0);
            $table->string('head_type');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('updateable')->default(1);
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
        Schema::dropIfExists('coa_setups');
    }
};
