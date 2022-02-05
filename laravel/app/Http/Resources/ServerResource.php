<?php

namespace App\Http\Resources;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var User $resource */
        $resource = $this->resource;
        return array_merge_recursive(UserResource::make($resource)->toArray(request()), ['services' => Service::where('user_id', $resource->id)->get()]);
    }
}
