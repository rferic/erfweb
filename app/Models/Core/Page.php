<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\App;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [ 'user_id' ];

    protected static function boot ()
    {
        parent::boot();

        static::deleting (function ($page) {
            if ($page->forceDeleting && !App::runningInConsole()) {
                $page->contents()->forceDelete();
                $page->menuItems()->forceDelete();
                $page->locales()->forceDelete();
                $page->childs()->forceDelete();
            }
        });
    }

    public function locales ()
    {
        return $this->hasMany(PageLocale::class);
    }

    public function author ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent ()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function childs ()
    {
        return $this->hasMany(Page::class);
    }

    public function isAuthor ()
    {
        return $this->author->id === auth()->id();
    }
    
    public function contents ()
    {
        return $this->hasManyThrough(Content::class, PageLocale::class)->withTrashed();
    }
    
    public function menuItems ()
    {
        return $this->hasManyThrough(MenuItem::class, PageLocale::class);
    }
}
