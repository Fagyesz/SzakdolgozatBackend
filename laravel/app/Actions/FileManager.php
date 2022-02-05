<?php

namespace App\Actions;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Str;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileManager
{
    /**
     * Upload a file
     * @param string $fileInputName File input name as given in `name` HTML attribute. Default: "file"
     * @param string $path Target directory inside '~/public/uploads/' folder. Default: ""
     * @param bool $throwErrorOnMissingFile If set to false, the function returns false on error instead of throwing an error. Default: false
     * @return string|bool Path of uploaded file, false on error
     * @throws BadRequestHttpException if request doesn't have input named $fileInputName
     * @throws FileException if file couldn't be moved or created
     */
    public static function upload(string $fileInputName = 'file', string $path = '', bool $throwErrorOnMissingFile = false): string|bool {
        $hasFile = request()->hasFile($fileInputName);

        if (!$hasFile && $throwErrorOnMissingFile) throw new BadRequestHttpException("Request does not have input file named $fileInputName");

        if ($hasFile) {
            $uploadedFile = request()->file($fileInputName);
            return $uploadedFile->move(tenant('id') . "/uploads/$path", Str::random(8) . '.' . $uploadedFile->getClientOriginalExtension())->getPath();
        }
        return false;
    }

    /**
     * @throws FileNotFoundException
     */
    public static function delete(string $path, bool $throwErrorOnMissingFile = false): bool{
        if ($throwErrorOnMissingFile && !File::exists($path)) throw new FileNotFoundException();

        return File::delete($path);
    }

    public static function uploadBase64(string $path, string $fileInputName = 'file'): string
    {
        $type = explode('/', request($fileInputName)['mime'])[1];
        $data = request($fileInputName)['data'];
        $imageName = Str::random(8) . '.' . $type;
        $binary = base64_decode($data, true);
        $src = Str::of(tenant('id') . "/uploads/$path")->ensureEnd('/') . $imageName;
        try {
            if(!file_exists(dirname(public_path($src))))
                mkdir(dirname(public_path($src)), 0777, true);
            $put = file_put_contents(public_path($src), $binary);
        } catch (\Exception $e){
            throw new AccessDeniedException(public_path($src));
        }
        return $src;
    }
}
