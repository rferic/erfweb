<?php

namespace App\Models\Core;

use App\Http\Helpers\ImageHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = [ 'src', 'title' ];

    public static $folder = 'images';

    protected static function boot ()
    {
        parent::boot();

        static::deleted (function ($image) {
            ImageHelper::destroyImage(self::$folder, $image->src);
        });
    }

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
