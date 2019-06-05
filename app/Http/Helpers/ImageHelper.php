<?php

namespace App\Http\Helpers;

use App\Models\Core\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    static public $disk = 'public';
    static public $temporalPath = '_tmp';

    static function upload ( $image )
    {
        $imagePath =  Storage::disk( self::$disk )->putFile(self::$temporalPath, $image, self::$disk);
        return $imagePath;
    }

    static function move ( $origin, $destination )
    {
        if ( Storage::disk( self::$disk )->exists( $origin ) ) {
            Storage::disk( self::$disk )->move( $origin, $destination );
        }

        return $destination;
    }

    static function destroyDirectory ( $path )
    {
        Storage::disk( self::$disk )->deleteDirectory($path);
    }

    static function destroyImage ( $path, $src )
    {
        $imageInfo = pathinfo($src);
        $origin = $path . '/' . $imageInfo['basename'];

        if ( Storage::disk( self::$disk )->exists( $origin ) ) {
            Storage::disk( self::$disk )->delete( $origin );
        }
    }

    static function getMetadata ( $image )
    {
        return [
            'mimeType' => Storage::disk(self::$disk)->exists($image) ? Storage::disk(self::$disk)->mimeType($image) : null,
            'size' => Storage::disk(self::$disk)->exists($image) ? Storage::disk(self::$disk)->size($image) : null
        ];
    }

    static function storeImageTemporal ( $imageTmp, $imageNew )
    {
        if ( Storage::disk(self::$disk)->exists($imageTmp) ) {
            // Remove image if exists
            if ( Storage::disk(self::$disk)->exists($imageNew) ) {
                Storage::disk(self::$disk)->delete($imageNew);
            }
            // Move temporal image to static folder
            Storage::disk(self::$disk)->move($imageTmp, $imageNew);
            return $imageNew;
        }

        return $imageTmp;
    }

    static function removeImageTemporal ( $imageOld )
    {
        if ( Storage::disk(self::$disk)->exists($imageOld) ) {
            Storage::disk(self::$disk)->delete($imageOld);
        }
    }
}
