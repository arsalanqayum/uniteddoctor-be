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
        Schema::create('eperiances', function (Blueprint $table) {
            $table->id();
            $table->string('jobTitle');
            $table->text('description');
            $table->string('employer');
            $table->date('startDate');
            $table->date('endDate');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eperiances');
    }
};
