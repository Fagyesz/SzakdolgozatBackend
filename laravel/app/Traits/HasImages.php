<?php


namespace App\Traits;


use App\Actions\FileManager;
use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property Collection images
 * @method MorphToMany morphToMany(string $class, string $string)
 */
trait HasImages
{
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public function deleteAllImages(){
        $this->images()->each(function (Image $image){
            $this->images()->detach($image);
            $image->delete();
        });
    }
}
