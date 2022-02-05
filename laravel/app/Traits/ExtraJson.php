<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

trait ExtraJson
{
    protected function getConnectedProps(): array
    {
        return array_diff(
            get_class_methods($this),
            get_class_methods(Model::class),
                get_class_methods(UUID::class),
                get_class_methods(HasFactory::class),
                get_class_methods(ApiResource::class)
           );
    }

    public function __get($key){
        if (array_search($key, array_keys($this->getAttributes())) || array_search($key, $this->getConnectedProps()) || $key === $this->getKeyName()) return $this->getAttribute($key);

        return $this->getExtraAsArray()[$key] ?? null;
    }

    public function __set($key, $value){
        if (array_key_exists($key, $this->getAttributes())
            || array_search($key, $this->getConnectedProps())
            || $key ===  $this->getKeyName()
            || (array_search($key, ['created_at', 'updated_at', 'deleted_at']) !== false)
        ) return $this->setAttribute($key, $value);
        $original = $this->getExtraAsArray();
        $new = [$key => $value];
        $this->extra = json_encode(array_merge_recursive_distinct($original,$new));

        return true;
    }

    private function getExtraAsArray(): array
    {
        if (!isset($this->extra)) {
            return [];
        }
        return (is_string($this?->extra) ? json_decode($this?->extra ?? '',true, flags: JSON_OBJECT_AS_ARRAY) : $this?->extra) ?? [];
    }
}
