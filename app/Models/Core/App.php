<?php

namespace App\Models\Core;

use Arcanedev\Localization\Facades\Localization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

use App\Http\Helpers\ImageHelper;

class App extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'page_id', 'status', 'version', 'vue_component', 'type', 'status' ];
    protected $appends = [ 'imagePath', 'pageLocale' ];

    protected static function boot ()
    {
        parent::boot();

        static::deleting (function ($app) {
            if ( $app->forceDeleting ) {
                ImageHelper::destroyDirectory($app->imagePath);

                $app->images()->forceDelete();
                $app->locales()->forceDelete();
                $app->users()->detach();
            }
        });
    }

    public function page ()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function locales ()
    {
        return $this->hasMany(AppLocale::class);
    }

    public function locale ()
    {
        return $this->hasOne(AppLocale::class)->where('lang', Localization::getCurrentLocaleRegional());
    }

    public function images ()
    {
        return $this->hasMany(AppImage::class)->orderBy('priority', 'asc');
    }

    public function imagesLocalization ()
    {
        return $this->hasMany(AppImage::class)->where('langs', 'like', '%' . Localization::getCurrentLocaleRegional() . '%')->orderBy('priority', 'asc');
    }

    public function users ()
    {
        return $this->belongsToMany(User::class)->using(AppUser::class)->withTimestamps()->withPivot('active');
    }

    public function getImagePathAttribute ()
    {
        return 'apps/' . $this->id;
    }

    public function getPageLocaleAttribute ()
    {
        return PageLocale::where('page_id', $this->page_id)
            ->where('lang', Localization::getCurrentLocaleRegional())
            ->get()
            ->first();
    }

    public function getCreatedAtAttribute ( $date ) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
