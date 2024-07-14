<?php
namespace App\Filters;

use App\Models\Service;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PCPListFilter
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function apply($query)
    {
        if (request()->filled('status')) {
            $query->where('status', request()->get('status'));
        }
        if (request()->filled('patient_name')) {
            $query->whereHas('patient', function ($query) {
                $query->where('first_name', 'like', '%'.request()->get('first_name').'%');
            });
        }
        if (request()->filled('doctor_name')) {
            $query->whereHas('doctor', function ($query) {
                $query->where('first_name', request()->get('doctor_name'));
            });
        }


        return $query;
    }
}

