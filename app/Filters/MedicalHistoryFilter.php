<?php
namespace App\Filters;

class MedicalHistoryFilter
{
    public function apply($query)
    {
        if (request()->filled('patient_id')) {
            $query->where('patient_id', request()->get('patient_id'));
        }

        return $query;
    }
}
?>