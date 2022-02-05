<?php

namespace App\Http\Resources;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var User $resource */
        $resource = $this->resource;
        return array_merge(
            $resource->withoutRelations()->toArray(),
            ['extra' => json_decode($resource->extra)],
            ['roles' => $resource->roles],
            ['images' => $resource->images],
            ($resource->is_server ? ['services' => Service::where('user_id', $resource->id)->with('images')->get()] : [])
        );
    }
}
