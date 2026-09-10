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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_transaction_auto_id')->constrained()->onDelete('cascade');
            $table->string('voucher_no');
            $table->string('voucher_type', 20);
            $table->date('voucher_date');
            $table->foreignId('coa_setup_id');
            $table->bigInteger('coa_head_code');
            $table->text('narration');
            $table->decimal('debit_amount', 16, 2)->default(0.00);
            $table->decimal('credit_amount', 16, 2)->default(0.00);
            $table->string('document')->nullable();
            $table->boolean('posted')->default(0);
            $table->boolean('approve')->default(0);
            $table->foreignId('approve_by')->nullable();
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
        Schema::dropIfExists('account_transactions');
    }
};
