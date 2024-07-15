<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDayAndValidUntilToDoctorAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            $table->string('day')->nullable()->after('date');
            $table->date('validUntil')->nullable()->after('day');
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
            $table->dropColumn('day');
            $table->dropColumn('validUntil');
        });
    }
}

