<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            // Temporarily drop the foreign key constraint if exists
            $table->dropForeign(['location_id']); // Adjust the name if different

            // Allow NULL values for location_id temporarily
            $table->unsignedBigInteger('location_id')->nullable()->change();
        });

        // Use raw SQL to update empty string values to NULL
        DB::statement("UPDATE doctor_availabilities SET location_id = NULL WHERE location_id = ''");

        Schema::table('doctor_availabilities', function (Blueprint $table) {
            // Continue with the other changes
            $table->string('location')->nullable()->change();
            $table->string('location_address')->nullable()->change();
            $table->decimal('latitude', 10, 8)->nullable()->change();
            $table->decimal('longitude', 11, 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->string('location_address')->nullable(false)->change();
            $table->decimal('latitude', 10, 8)->nullable(false)->change();
            $table->decimal('longitude', 11, 8)->nullable(false)->change();
        });
    }
};
