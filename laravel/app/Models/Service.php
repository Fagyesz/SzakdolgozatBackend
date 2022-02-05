<?php

namespace App\Models;

use App\Traits\ApiResource;
use App\Traits\ExtraJson;
use App\Traits\HasImages;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array extra
 * @property User user
 * @property int duration
 */
class Service extends Model
{
    use UUID, HasFactory, ApiResource, HasImages, ExtraJson;

    protected $fillable = ['name', 'extra', 'description', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
