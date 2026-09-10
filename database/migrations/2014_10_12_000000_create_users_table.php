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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('role')->default(0);
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('user_name')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('image')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('otp')->nullable();
            $table->dateTime('otp_expire')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
