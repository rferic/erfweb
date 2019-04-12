<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

use App\Http\Helpers\ImageHelper;

class App extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'status', 'version', 'vue_component', 'type', 'status' ];

    protected static function boot ()
    {
        parent::boot();

        static::deleting (function ($app) {
            if ( $app->forceDeleting ) {
                ImageHelper::destroyDirectory($app->imagePath());

                $app->images()->forceDelete();
                $app->locales()->forceDelete();
                $app->users()->detach();
            }
        });
    }

    public function locales ()
    {
        return $this->hasMany(AppLocale::class);
    }

    public function images ()
    {
        return $this->hasMany(AppImage::class)->orderBy('priority', 'asc');
    }

    public function users ()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('active');
    }

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }

    public function imagePath ()
    {
        return 'apps/' . $this->id;
    }
}
