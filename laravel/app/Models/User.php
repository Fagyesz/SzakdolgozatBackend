<?php

namespace App\Models;

use App\Traits\ApiKey;
use App\Traits\ApiResource;
use App\Traits\ExtraJson;
use App\Traits\HasImages;
use App\Traits\UUID;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
//use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * @property string name
 * @property string email
 * @property string password
 * @property string extra
 * @property bool troll
 * @property string remember_token
 * @property string api_key
 * @property DateTime email_verified_at
 * @property bool is_server
 * @property array[] working_hours
 * @property Collection roles
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, ApiResource, UUID, HasRolesAndAbilities, ApiKey, HasApiTokens, HasImages, ExtraJson;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_key', 'extra'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'troll' => 'boolean'
    ];

    protected $appends = ['is_server'];

    public function getIsServerAttribute(): bool
    {
        return ($this?->isA('server'));
    }

    public function appointment(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
