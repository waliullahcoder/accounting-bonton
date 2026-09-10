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
        Schema::create('admin_menu_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id');
            $table->foreignId('admin_menu_id');
            $table->string('name')->nullable();
            $table->string('route');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_menu_actions');
    }
};
