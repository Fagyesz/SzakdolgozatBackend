<?php

namespace App\Models;

use App\Actions\FileManager;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ApiResource;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class Image
 * @package App\Models
 *
 * @property string src
 */

class Image extends Model
{
    use HasFactory, ApiResource, UUID;
    protected $fillable = ['src'];

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'imageable');
    }

    protected static function booted()
    {
        static::deleting(fn(Image $image) => FileManager::delete(public_path($image->src), true));
    }
}
