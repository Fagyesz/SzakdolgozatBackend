<?php

namespace Database\Seeders;

use App\Actions\WorkingHours\WorkingHour;
use App\Models\User;
use App\Utils\Bouncer;
use Illuminate\Database\Seeder;

class BouncerSeeder extends Seeder
{
    public function run(): void
    {
        Bouncer::allow('superuser')->everything();
        User::sample(2)->each(fn (User $user) => $user->assign('assistant'));
        User::sample(5)->each(fn (User $user) => self::assignServer($user));

        /** @var User $admin */
        $admin = User::where('email', 'admin@test.com')->first();
        $admin->assign('superuser');
    }

    static function assignServer(User $user): bool
    {
        $user->assign('server');
        $user->working_hours = WorkingHour::getExampleWorkingHour($user);
        $user->save();
        return true;
    }
}
