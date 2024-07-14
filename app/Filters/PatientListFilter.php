<?php
namespace App\Filters;

use App\Models\Service;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PatientListFilter
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function apply($query)
    {
        if (request()->filled('first_name')) {
            $query->where('first_name', 'like', '%'.request()->get('first_name').'%');
        }
        if (request()->filled('doctor_id')) {
            $query->where('doctor_id', request()->get('doctor_id'));
        }
        if (request()->filled('patient_id')) {
            $query->where('patient_id', request()->get('patient_id'));
        }

        return $query;
    }
}

