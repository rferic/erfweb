<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static $disk = 'public';
    public static $pathTemporal = '_tmp';

    static function upload ( $image )
    {
        $imagePath =  Storage::disk( self::$disk )->putFile(self::$pathTemporal, $image, self::$disk);
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
}
