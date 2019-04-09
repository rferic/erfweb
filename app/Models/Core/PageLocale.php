<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\App;

class PageLocale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'page_id',
        'lang',
        'slug',
        'title',
        'description',
        'layout',
        'options',
        'seo_title',
        'seo_description',
        'seo_keywords'
    ];

    protected static function boot ()
    {
        parent::boot();

        static::deleting (function ($page_locale) {
            if (!App::runningInConsole()) {
                $page_locale->contents()->forceDelete();
                $page_locale->menuItems()->forceDelete();
            }
        });
        
        static::deleted (function ($page_locale) {
            if (!App::runningInConsole()) {
                $page = $page_locale->page();

                if (PageLocale::where('page_id', $page_locale->page_id)->get()->count() < 1) {
                    $page->forceDelete();
                }
            }
        });
    }

    public function page ()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function contents ()
    {
        return $this->hasMany(Content::class)->withTrashed();
    }

    public function author ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAuthor ()
    {
        return $this->author->id === auth()->id();
    }
    
    public function menuItems ()
    {
        return $this->hasMany(MenuItem::class);
    }
}
