<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            // Add the new column with the desired type
            $table->string('location_id_new', 255)->nullable();
        });

        // Copy data from the old column to the new column
        DB::statement('UPDATE doctor_availabilities SET location_id_new = location_id');

        // Drop the old column
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });

        // Rename the new column to the original name
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->renameColumn('location_id_new', 'location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            // Add the old column back
            $table->unsignedBigInteger('location_id_old')->nullable();

            // Copy data back to the old column
            DB::statement('UPDATE doctor_availabilities SET location_id_old = location_id');

            // Drop the new column
            $table->dropColumn('location_id');

            // Rename the old column back to the original name
            Schema::table('doctor_availabilities', function (Blueprint $table) {
                $table->renameColumn('location_id_old', 'location_id');
            });
        });
    }
};
