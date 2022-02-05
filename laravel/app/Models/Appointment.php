<?php

namespace App\Models;

use App\Traits\ApiResource;
use App\Traits\UUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property Service service
 * @property User user
 * @property Carbon begin_time
 */
class Appointment extends Model
{
    use HasFactory, ApiResource, UUID;

    protected $fillable = ['begin_time', 'end_time', 'service_id', 'user_id', 'note'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function server(): BelongsTo
    {
        return $this->service()->get()->user();
    }
}
