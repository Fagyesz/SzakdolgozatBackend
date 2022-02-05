<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utils\StatusCode;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        if (!Auth::user()?->can('index', User::class)) abort(StatusCode::FORBIDDEN);

        return User::query()
            ->where('name', 'LIKE', '%' . request('query', '') . '%')
            ->orWhere('email', 'LIKE', '%' . request('query', '') . '%')
            ->with('images')
            ->with('roles')
            ->paginate(request('per_page', User::count()));
    }

    public function show(User $user): JsonResponse
    {
        $this->protect('show', $user);

        return response()->json(UserResource::make($user), StatusCode::OK);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $this->protect('edit', $user);

        $user->update($request->except('image_id'));
        if ($request->has('image_id')) {
            $user->deleteAllImages();
            $user->images()->attach($request->image_id);
        }
        $user->save();

        return response()->json(UserResource::make($user), StatusCode::ACCEPTED);
    }

    public function destroy(User $user): JsonResponse {
        $this->protect('destroy', $user);

        $user->deleteAllImages();
        $user->delete();

        return response()->json(UserResource::make($user), StatusCode::ACCEPTED);
    }

    public function setTroll(User $user):Response|Application|ResponseFactory
    {
        if (!Auth::user()?->can('mark-troll', User::class)) abort(StatusCode::FORBIDDEN);

        $user->troll = (bool)request('troll', false);
        $user->save();
        return response(null, StatusCode::ACCEPTED);
    }

    public function setWorkHour(User $user): Response|Application|ResponseFactory
    {
        $workingHours = request('working_hours');
        $user->forceFill(['extra->working_hours' => $workingHours]);
        $user->save();
        return response(null, StatusCode::ACCEPTED);
    }

    private function protect(string $ability, User $user): void
    {
        if (!Auth::user()?->can($ability, User::class) && $user->id !== Auth::id()) abort(StatusCode::FORBIDDEN);
    }

}
