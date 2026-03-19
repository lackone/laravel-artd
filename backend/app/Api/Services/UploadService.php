<?php

namespace App\Api\Services;

use Illuminate\Http\UploadedFile;

class UploadService
{
    /**
     * 上传一个文件
     * @param UploadedFile $file
     * @return array
     */
    public static function uploadOne(UploadedFile $file)
    {
        $path = $file->store(date('Ym') . '/' . date('d'), 'uploads');
        $path = '/uploads/' . ltrim($path, '/');
        $url = '//' . preg_replace('/^https?:\/\//', '', asset($path));

        return [
            'ext' => $file->extension(),
            'path' => $path ? ('/' . ltrim($path, '/')) : '',
            'url' => $url,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ];
    }
}
