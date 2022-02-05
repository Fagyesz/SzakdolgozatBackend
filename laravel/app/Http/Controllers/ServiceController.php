<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServerResource;
use App\Http\Resources\UserResource;
use App\Models\Service;
use App\Models\User;
use App\Utils\StatusCode;
use Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        $serverId = Auth::user()->is_server ? Auth::id() : request('server', '');
        $services = Service::where('user_id', 'LIKE', '%' . $serverId . '%')
            ->with('user')
            ->with('images');

//        if (Auth::user()?->can('index', '\App\Models\Appointments')) {
//            $services = $services->with('appointments');
//        }

        return $services->paginate(request('per_page', Service::count()));
    }

    public function show(Service $service): JsonResponse
    {
        return $this->getResponse($service);
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        if (!Auth::user()?->can('create', Service::class)) abort(StatusCode::FORBIDDEN);
        /** @var Service $service */
        $service = Service::create($request->except('image_id'));
        $service->images()->attach($request->image_id);
        $service->save();

        return $this->getResponse($service, StatusCode::CREATED);
    }

    public function update(ServiceRequest $request, Service $service): JsonResponse {
        if (!Auth::user()?->can('edit', Service::class)) abort(StatusCode::FORBIDDEN);

        $service->update($request->except('image_id'));
        if ($request->has('image_id')) {
            $service->deleteAllImages();
            $service->images()->attach($request->image_id);
        }
        $service->save();
        return $this->getResponse($service, StatusCode::ACCEPTED);
    }

    public function destroy(Service $service): JsonResponse
    {
        if (!Auth::user()?->can('destroy', Service::class)) abort(StatusCode::FORBIDDEN);

        $service->delete();

        return $this->getResponse($service, StatusCode::ACCEPTED);
    }

    public function getServicesFor(User $user)
    {
        return ServerResource::make($user);
    }

    private function getResponse(Service $service, int $statusCode = StatusCode::OK): JsonResponse
    {
        return response()->json(array_merge(
                                    $service->toArray(),
                                    ['images' => $service->images],
                                    ['server' => $service->user()->get()],
                                ),
                                $statusCode
        );
    }
}
