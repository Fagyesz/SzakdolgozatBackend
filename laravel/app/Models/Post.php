<?php

namespace App\Models;

use App\Traits\ApiResource;
use App\Traits\HasImages;
use App\Traits\UUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string title
 * @property string content
 * @property Carbon published_at
 * @property User user
 * @property string user_id
 * @property boolean is_published
 */
class Post extends Model
{
    use HasFactory, ApiResource, HasImages, UUID;

    protected $fillable = [
        'title',
        'content',
        'published_at'
    ];

    protected $appends = [
      'is_published'
    ];

    protected $casts = [
        'published_at' => 'datetime:Y-m-d H:i:s',
        'is_published' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->published_at && $this->published_at->lte(now());
    }
}
