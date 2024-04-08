<?php

namespace Database\Seeders;

use App\Models\Widget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Widget::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $parent = Widget::firstOrCreate(['widget_name' => 'Doctor', 'type' => 'doctor', 'cols' => 3, 'newRow' => false, 'parent_id' => null]);
        $widgets = [
            ['widget_name' => 'Appointments', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Next Patients', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Telehealth Visits', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Earnings', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Income', 'type' => 'lineGraph', 'cols' => 6, 'newRow' => true, 'parent_id' => $parent->id],
            ['widget_name' => 'Patients Gender', 'type' => 'pieGraph', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'New Patients', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Existing Patients', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Wellness Score', 'type' => 'barGraph', 'cols' => 6, 'newRow' => true, 'parent_id' => $parent->id],
            ['widget_name' => 'Recent Appointments', 'type' => 'aptTime', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Recent Appointments', 'type' => 'apttable', 'cols' => 5, 'newRow' => true, 'parent_id' => $parent->id],
            ['widget_name' => 'Total Hours', 'type' => 'pieGraphHours', 'cols' => 5, 'newRow' => false, 'parent_id' => $parent->id],
        ];
        for ($i = 0; $i < count($widgets); $i++) {
            Widget::firstOrCreate(['widget_name' => $widgets[$i]['widget_name'], 'type' => $widgets[$i]['type'], 'cols' => $widgets[$i]['cols'], 'newRow' => $widgets[$i]['newRow'], 'parent_id' => $widgets[$i]['parent_id']]);
        }
        $parent = Widget::firstOrCreate(['widget_name' => 'patient', 'type' => 'patient', 'cols' => 3, 'newRow' => false, 'parent_id' => null]);
        $widgets = [
            ['widget_name' => 'Appointments', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'General Checkup', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Consultation', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Specialist Visit', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Static of your Health', 'type' => 'lineGraph', 'cols' => 6, 'newRow' => true, 'parent_id' => $parent->id],
            ['widget_name' => 'Body Mass index', 'type' => 'pieGraph', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Heart Rate', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Temprature', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Blood Pressure', 'type' => 'barGraph', 'cols' => 6, 'newRow' => true, 'parent_id' => $parent->id],
            ['widget_name' => 'Sleep', 'type' => 'aptTime', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
         
        ];
        for ($i = 0; $i < count($widgets); $i++) {
            Widget::firstOrCreate(['widget_name' => $widgets[$i]['widget_name'], 'type' => $widgets[$i]['type'], 'cols' => $widgets[$i]['cols'], 'newRow' => $widgets[$i]['newRow'], 'parent_id' => $widgets[$i]['parent_id']]);
        }
        $parent = Widget::firstOrCreate(['widget_name' => 'admin', 'type' => 'admin', 'cols' => 3, 'newRow' => false, 'parent_id' => null]);
        $widgets = [
            ['widget_name' => 'Total number of patients', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Total number of doctors', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Total number of appointments', 'cols' => 3, 'newRow' => false, 'type' => 'count', 'parent_id' => $parent->id],
            ['widget_name' => 'Total number of prescriptions filled', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Patient satisfaction ratings', 'type' => 'lineGraph', 'cols' => 6, 'newRow' => true, 'parent_id' => $parent->id],
            ['widget_name' => 'Average wait times', 'type' => 'pieGraph', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Appointment cancellation rates', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id],
            ['widget_name' => 'Patient satisfaction ratings', 'type' => 'count', 'cols' => 3, 'newRow' => false, 'parent_id' => $parent->id]
           
         
        ];
        for ($i = 0; $i < count($widgets); $i++) {
            Widget::firstOrCreate(['widget_name' => $widgets[$i]['widget_name'], 'type' => $widgets[$i]['type'], 'cols' => $widgets[$i]['cols'], 'newRow' => $widgets[$i]['newRow'], 'parent_id' => $widgets[$i]['parent_id']]);
        }
        // $parent = Widget::firstOrCreate(['widget_name'=>'Patient', 'parent_id'=>null]);
        // $widgets = [
        //     ['widget_name'=> 'Next appointment timer', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'My doctors list', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'My future appointments', 'parent_id' => $parent->id],
        // ];
        // for ($i=0; $i < count($widgets); $i++) { 
        //     Widget::firstOrCreate(['widget_name'=>$widgets[$i]['widget_name'], 'parent_id'=>$widgets[$i]['parent_id']]);
        // }
        // $parent = Widget::firstOrCreate(['widget_name'=>'Staff', 'parent_id'=>null]);
        // $widgets = [
        //     ['widget_name'=> 'Wellness', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Stats 1', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Appointments', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Recent Appoint', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Patients', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'New Patients', 'parent_id' => $parent->id],
        // ];
        // for ($i=0; $i < count($widgets); $i++) { 
        //     Widget::firstOrCreate(['widget_name'=>$widgets[$i]['widget_name'], 'parent_id'=>$widgets[$i]['parent_id']]);
        // }
        // $parent = Widget::firstOrCreate(['widget_name'=>'Admin', 'parent_id'=>null]);
        // $widgets = [
        //     ['widget_name'=> 'Total no of doctors', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Total no of patient', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Total no of staffs', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Total no of members', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Latest 5 registered patient', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Latest 5 registered doctor', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Website or system viewer or visitor analytics', 'parent_id' => $parent->id],
        //     ['widget_name'=> 'Total appointment today', 'parent_id' => $parent->id],
        // ];
        // for ($i=0; $i < count($widgets); $i++) { 
        //     Widget::firstOrCreate(['widget_name'=>$widgets[$i]['widget_name'], 'parent_id'=>$widgets[$i]['parent_id']]);
        // }
    }
}
