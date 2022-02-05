<?php

namespace App\Http\Controllers;

use App\Actions\WorkingHours\WorkingHour;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Utils\StatusCode;
use Auth;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Cmixin\BusinessTime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use function PHPUnit\Framework\assertIsBool;

class AppointmentController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        /** @var QueryBuilder $querry */
        $query = null;
        if (Auth::user()->is_server) {
            $query = $this
                ->getAppointmentsFor(Auth::id())
                ->with('user')
                ->with('user.images');
        } elseif (Auth::user()->can('see-all-appointments')) {
            $query = $this
                ->getAppointmentsFor(request('server', ''))
                ->with('user')
                ->with('user.images')
                ->with('service.user') //With Server
                ->with('service.user.images');
        } else {
            $query = Appointment::where('user_id', Auth::id())
                ->with('service.user') //With Server
                ->with('service.user.images');
        }

        if (!request('past', false)) {
            $query->where('begin_time', '>=', now());
        }

        return $query
            ->with('service')
            ->with('service.images')
            ->orderBy('begin_time')
            ->paginate(request('per_page', $query->count()));
    }

    public function show(Appointment $appointment): JsonResponse
    {
        if ($appointment->user->id !== Auth::id() && Auth::user()->cannot('see-all-appointments')) abort(StatusCode::FORBIDDEN);
        return $this->getAppointmentResponse($appointment);
    }

    public function update(AppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        if (Auth::user()->cannot('edit', Appointment::class)) abort(StatusCode::FORBIDDEN);

        $appointment->update($request->validated());
        $appointment->save();

        return $this->getAppointmentResponse($appointment, StatusCode::ACCEPTED);
    }

    public function store(AppointmentRequest $request): JsonResponse
    {
        $rq = $request->validated();


        if (Auth::user()->cannot('create', Appointment::class)){
            $rq['user_id'] = Auth::id();
            if (!$request->has('token')) {
                abort(StatusCode::BAD_REQUEST, 'Appointment token was not present');
            }
        }

        if ($request->has('token')) {
            $rq = WorkingHour::getFromId($rq['token']);
        }

        return $this->getAppointmentResponse(
            Appointment::create(json_decode(json_encode($rq), true)),
            StatusCode::CREATED
        );
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        if (Auth::user()->cannot('delete', Appointment::class) && $appointment->user->id !== Auth::id()) abort(StatusCode::FORBIDDEN);

        if ($appointment->user->id === Auth::id() && $appointment->begin_time->lte(now()->addDays(3))) abort(StatusCode::NOT_ACCEPTABLE);

        $appointment->delete();

        return $this->getAppointmentResponse($appointment, StatusCode::ACCEPTED);
    }

    public function recommend(Service $service, User $user): array
    {
        return WorkingHour::getRecommendedTimes($user,$service);
    }

    private function getAppointmentsFor(string $id): EloquentBuilder
    {
        return Appointment::whereIn('service_id', function (QueryBuilder $query) use ($id) {
            $with = with(new Service);
            $query
                ->from($with->getTable())
                ->select($with->getKeyName())
                ->where('user_id', 'LIKE', '%' . $id . '%');
        });
    }

    private function getAppointmentResponse(Appointment $appointment, int $statusCode = StatusCode::OK): JsonResponse
    {
        return response()->json(
            array_merge_recursive(
                $appointment->toArray(),
                ['client' => $appointment->user()->with('images')->get()],
                ['service' => $appointment->service()->with('images')->with('user')->with('user.images')->get()]
            ),
            $statusCode
        );
    }
}
