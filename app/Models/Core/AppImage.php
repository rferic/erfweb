<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Storage;

use App\Http\Controllers\Admin\AppImageController;

class AppImage extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'app_id', 'src', 'title', 'priority' ];

    protected static function boot ()
    {
        parent::boot();

        static::deleted (function ($appImage) {
            if ( $appImage->forceDeleting ) {
                AppImageController::destroy($appImage);
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
