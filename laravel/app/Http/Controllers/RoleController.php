<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utils\StatusCode;
use Auth;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Silber\Bouncer\Database\Role;

class RoleController extends Controller
{

    public function index(): array
    {
        $result = [];
        /** @var Collection $users */
        Role::where('name', request('q', 'server'))->first()?->users()?->each(function (User $user) use (&$result) {
            $ok = false;
            foreach ($user->roles as $role){
                if ($role->name === request('q', 'server')) {
                    $ok = true;
                    break;
                }
            }
            if ($ok) $result[] = UserResource::make($user);
        });
        return $result;
    }

    public function assign(User $user, string $role): Response|Application|ResponseFactory
    {
        return self::roleManage(fn() => $user->assign($role));
    }

    public function revoke(User $user, string $role): Response|Application|ResponseFactory
    {
        return self::roleManage(fn() => $user->retract($role));
    }

    private static function roleManage(Closure $closure): ResponseFactory|Application|Response
    {
        if (!Auth::user()->can('manage', Role::class)) abort(StatusCode::FORBIDDEN);
        $closure()->update();
        return response(null, StatusCode::ACCEPTED);
    }
}
