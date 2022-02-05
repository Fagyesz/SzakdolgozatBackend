<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Image;
use App\Models\Post;
use App\Utils\Bouncer;
use App\Utils\StatusCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    private static function deleteImages(Post $post): void
    {
        $post->images()->each(function (Image $image) use ($post) {
            $post->images()->detach($image);
            $image->delete();
        });
    }

    private static function getPostResponse(Post $post, int $statusCode = StatusCode::OK): JsonResponse
    {
        return response()->json(array_merge($post->toArray(), ['images' => $post->images]), $statusCode);
    }

    private static function canNotSeeUnpublished(): bool
    {
        return !Auth::user()?->can('see_unpublished_posts');
    }

    public function index(): LengthAwarePaginator
    {
        $posts = Post::query()->with('images');
        if (self::canNotSeeUnpublished()) {
            $posts->whereNotNull('published_at')->Where('published_at', '<=', now());
        } else if (request('drafts', false)) {
            $posts->whereNull('published_at')->orWhere('published_at', '>=', now());
        }
        return $posts->latest()->paginate(request('per_page', $posts->count()));
    }

    public function store(PostRequest $request): JsonResponse
    {
        if(!Bouncer::can('create', Post::class)) abort(StatusCode::FORBIDDEN);
        /** @var Post $post */
       $post = Post::make($request->except('image_id'));
       $post->user_id = Auth::id();
       $post->save();
       $post->refresh();
       if ($request->has('image_id'))
            $post->images()->attach($request->image_id);

        return self::getPostResponse($post, StatusCode::CREATED);
    }

    public function show(Post $post): JsonResponse
    {
        if (!$post->is_published && self::canNotSeeUnpublished()) abort(StatusCode::FORBIDDEN);

        return self::getPostResponse($post);
    }


    public function update(PostRequest $request, Post $post): JsonResponse
    {
        if(!Bouncer::can('edit', Post::class)) abort(StatusCode::FORBIDDEN);

        $post->update($request->except('image_id'));
        if ($request->has('image_id')) {
            self::deleteImages($post);
            if ($request->image_id !== 'NULL')
                $post->images()->attach($request->image_id);
        }
        $post->save();

        return self::getPostResponse($post, StatusCode::ACCEPTED);
    }

    public function destroy(Post $post): JsonResponse
    {
        if(!Bouncer::can('delete', Post::class)) abort(StatusCode::FORBIDDEN);

        self::deleteImages($post);
        $post->delete();
        return self::getPostResponse($post, StatusCode::ACCEPTED);
    }
}
