<?php

namespace App\Http\Controllers;

use App\Actions\FileManager;
use App\Http\Requests\ImageRequest;
use App\Models\Image;
use App\Traits\HasImages;
use App\Utils\StatusCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Str;

class ImageController extends Controller
{
    public function store(ImageRequest $request): JsonResponse
    {
        $src = FileManager::uploadBase64(path: 'posts');
        if (!$src) abort(StatusCode::UNPROCESSABLE_ENTITY);
        return response()->json(Image::create(['src' => $src]), StatusCode::CREATED);
    }
}
