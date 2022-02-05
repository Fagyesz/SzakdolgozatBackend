<?php

namespace App\Actions\WorkingHours;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Auth;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Cmixin\BusinessTime;
use DB;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Exception;
use Illuminate\Database\Eloquent\Collection as Collection;

class WorkingHour
{
    public static function getRecommendedTimes(User $user, Service $service): array
    {
        $recommended = [];
        BusinessTime::enable([Carbon::class, CarbonImmutable::class]);
        Carbon::setOpeningHours(WorkingHour::getExampleWorkingHour($user)); //TODO: Change to WorkingHour::getWorkingHours($user)
        CarbonImmutable::setOpeningHours(WorkingHour::getExampleWorkingHour($user)); //TODO: Change to WorkingHour::getWorkingHours($user)

        $current = self::getCurrentTime();
        $searchEnd = now()->addMonths(3);
        while (count($recommended) < 10 && $current->lt($searchEnd)) {
            $between = $current->isAm()
                ? [$current->setTime(0, 0), $current->setTime(11, 59)]
                : [$current->setTime(12, 0), $current->setTime(23, 59)];
            $earliestPossible = Appointment::query()
                ->where('service_id', $service->id)
                ->whereBetween('end_time', $between)
                ->max('end_time');
            if (!$earliestPossible) { //If there are no appointments on the chosen day
                if ($current->day === $current->nextOpen()->day) {
                    $current = $current->nextOpen();
                    $begin_time = $current->setSeconds(0)->nextMinuteMultipleOf(5);
                    WorkingHour::addIfFree($begin_time, $service, $recommended);
                }
            } else {
                //Here you can change the break time when finished an appointment after Carbon::parse with [->addMinutes(5)]
                $earliestPossible = Carbon::parse($earliestPossible)->toImmutable();
                $begin_time = $earliestPossible->toImmutable()->setSeconds(0)->nextMinuteMultipleOf(5);
                WorkingHour::addIfFree($begin_time, $service, $recommended);
            }
            $current = $current->addHours(12);
        }
        return $recommended;
    }

    private static function getCurrentTime()
    {
        return (now()->isAm()
                           ? Carbon::today()->setTime(13, 0)
                           : Carbon::tomorrow()->setTime(0, 1))
                           ->toImmutable();
    }

    private static function addIfFree($begin_time, Service $service, array &$recommended): void
    {
        $serverIsAvailable = self::isServerAvailable($begin_time, $service);
        $endTime = $begin_time->addMinutes($service->duration);
        if (/*self::doesFit($begin_time, $service) &&*/ $serverIsAvailable) {
            if ($begin_time->isOpen() && $endTime->isOpen()) { //checks if the service is open in the given time
                $recommended[] = [
                    'service_id' => $service->id,
                    'begin_time' => $begin_time->toDateTimeString(),
                    'end_time' => $endTime->toDateTimeString(),
                    'token' => self::register($begin_time, $endTime, $service->id)
                ];
            }
        }
    }

    private static function isServerAvailable($begin_time, Service $service): bool
    {
        /** @var Collection $appointmentsOfServer */
        $end_time = $begin_time->addMinutes($service->duration);
        $appointmentsOfServer = self::getAllAppointmentsOfServer($service->user_id);
        if (count($appointmentsOfServer) > 0)
        {
            $has_no_appointment_now = true;
            foreach ($appointmentsOfServer as $appointment)
            {
                if (!(($appointment->begin_time > $end_time && $appointment->begin_time > $begin_time) ||
                    ($appointment->end_time < $begin_time && $appointment->begin_time < $end_time)))
                {
                    $has_no_appointment_now = false;
                    break;
                }
            }
            return $has_no_appointment_now;
        }
        else
        {
            return true;
        }
    }

    private static function getAllAppointmentsOfServer($server)
    {
        $temp = new Collection();
        $services = Service::where('user_id', $server)->get();
        foreach ($services as $service)
        {
            $res = Appointment::where('service_id', $service->id)->where('end_time', '>=', now())->get();
            $temp = $temp->merge($res);
        }
        return $temp;
    }

//  Currently not needed because isServerAvailable() also checks this condition. but needs testing to make final decision
//
//     /** @noinspection PhpPureFunctionMayProduceSideEffectsInspection */
//     #[Pure] private static function doesFit(CarbonImmutable|Carbon|CarbonInterface $begin_time, Service $service): bool
//     {
//         $end_time = $begin_time->addMinutes($service->duration);
//         $nextAppointment = self::nextAppointment($service, $begin_time);
//         return $end_time < $nextAppointment; //TODO: FIX THIS!
//     }

//     /** @noinspection PhpPureFunctionMayProduceSideEffectsInspection */
//     #[Pure] private static function nextAppointment(Service $service, CarbonImmutable|Carbon|CarbonInterface $current): CarbonImmutable
//     {
//         /** @noinspection UnknownColumnInspection */
//         /** @noinspection PhpPossiblePolymorphicInvocationInspection */
//         $begin_time = Appointment::where('service_id', $service->id)
//             ->where('begin_time', '>', $current->toDateTimeString())
//             ->orderBy('begin_time')
//             ->first()?->begin_time;
//
//         return $begin_time
//             ? CarbonImmutable::parse($begin_time)
//             : $current->nextClose();
//     }

    #[Pure] public static function getExampleWorkingHour(User $user): array
    {
        return [
            'monday' => ['09:00-12:00', '13:00-18:00'],
            'tuesday' => ['09:00-12:00', '13:00-18:00'],
            'wednesday' => ['09:00-12:00'],
            'thursday' => ['09:00-12:00', '13:00-18:00'],
            'friday' => ['09:00-12:00', '13:00-20:00'],
            'saturday' => ['09:00-12:00', '13:00-16:00'],
            'sunday' => [],
            'exceptions' => [
                '2021-11-11' => ['09:00-10:00'],
                '2021-12-25' => [],
                '01-01' => [], // Recurring on each 1st of january
                '12-25' => ['09:00-12:00'], // Recurring on each 25th of december
            ],
            'holidaysAreClosed' => true,
            'holidays' => [
                'region' => 'hu-HU',
                'with' => [
                    'labor-day' => null,
                    'company-special-holiday' => '04-07',
                ],
            ],
        ];
    }

    private static function register(CarbonImmutable $begin_time, CarbonImmutable $endTime, string $service_id): string
    {
        $uuid = Str::uuid();
        DB::table('authed_appointments')->insert([
                                                     'id' => $uuid,
                                                     'user_id' => Auth::id(),
                                                     'begin_time' => $begin_time->toDateTimeString(),
                                                     'end_time' => $endTime,
                                                     'service_id' => $service_id
                                                 ]);
        return $uuid;
    }

    public static function getFromId(string $token): object|null
    {
        $result = DB::table('authed_appointments')->select()->where('id', $token)->first();
        DB::table('authed_appointments')->where('user_id', Auth::id())->delete();
        return $result;
    }
}
