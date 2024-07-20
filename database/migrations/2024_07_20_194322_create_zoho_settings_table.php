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
        Schema::create('zoho_settings', function (Blueprint $table) {
            $table->id();
            $table->text('token')->nullable();
            $table->bigInteger('expires')->nullable();
            $table->string('tenant_id')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('scope')->nullable();
            $table->string('api_domain')->nullable();
            $table->string('token_type')->nullable();
            $table->text('id_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoho_settings');
    }
};
