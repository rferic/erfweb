<?php

namespace App\Models\Core;

use App\Http\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AppImage extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'app_id', 'src', 'title', 'priority', 'langs' ];

    protected static function boot ()
    {
        parent::boot();

        static::deleted (function ($appImage) {
            if ( $appImage->forceDeleting ) {
                ImageHelper::destroyImage($appImage->app->imagePath(), $appImage->src);
            }
        });
    }

    public function app ()
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
