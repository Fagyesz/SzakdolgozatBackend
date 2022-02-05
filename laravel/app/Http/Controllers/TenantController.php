<?php

namespace App\Http\Controllers;

use Illuminate\Http\TenantRequest;
use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Utils\StatusCode;
use Illuminate\Http\JsonResponse;

class TenantController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        $tenants = Tenant::query();
        return $tenants->latest()->paginate(5);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        return $this->getResponse($tenant);
    }

    private function getResponse(Tenant $tenant, int $statusCode = StatusCode::OK): JsonResponse
    {
        return response()->json($tenant, $statusCode);
    }
}
