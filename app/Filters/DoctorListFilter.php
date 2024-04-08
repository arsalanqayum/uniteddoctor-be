<?php
namespace App\Filters;

use App\Models\Service;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DoctorListFilter
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function apply($query)
    {
        if (request()->filled('department_id')) {
            $query->where('department_id', request()->get('department_id'));
        }
        if (request()->filled('service_id')) {
            $service = Service::find(request()->get('service_id'));
            if ($service->service_type == 3){
                $userService = new UserService();
                $onlineUsers = $userService->getOnlineUsers();
                $onlineUsers = $onlineUsers->pluck('id')->toArray();
                $query->whereIn('user_id', $onlineUsers);
            }else{
                $query->whereHas('doctorServices', function ($query) {
                    $query->where('service_id', request()->get('service_id'));
                });
            }
        }

        return $query;
    }
}

