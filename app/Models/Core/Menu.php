<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class Menu extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'user_id', 'name', 'description' ];

    protected static function boot ()
    {
        parent::boot();

        static::deleting (function ($menu) {
            if ($menu->forceDeleting && !App::runningInConsole()) {
                $menu->items()->forceDelete();
            }
        });
    }

    public function items ()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function author ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAuthor ()
    {
        return $this->author->id === auth()->id();
    }

    public function getCreatedAtAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
    }
}
