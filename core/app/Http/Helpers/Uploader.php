<?php

namespace App\Http\Helpers;

class Uploader
{
    public static function upload_picture($directory, $img): string
    {
        $file_name = sha1(time() . rand());
        if(!file_exists($directory)){
            if (!mkdir($directory, 0777, true)) {
                die('Failed to create folders...');
            }
        }
        $ext = $img->getClientOriginalExtension();
        $newFileName = $file_name . "." . $ext;
        $img->move($directory, $newFileName);
        return $newFileName;
    }


    public static function update_picture($directory, $img, $old_img): string
    {
        $file_name = sha1(time() . rand());
        if(!file_exists($directory)){
            if (!mkdir($directory, 0777, true)) {
                die('Failed to create folders...');
            }
        }
        $ext = $img->getClientOriginalExtension();
        $newFileName = $file_name . "." . $ext;
        $oldImgPath = $directory. '/' . $old_img;
        if (file_exists($oldImgPath)) @unlink($oldImgPath);
        $img->move($directory, $newFileName);
        return $newFileName;
    }
}
