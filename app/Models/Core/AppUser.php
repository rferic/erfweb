<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AppUser extends Pivot
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [ 'active' ];

    public function user ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function app ()
    {
        return $this->belongsTo(App::class, 'app_id');
    }
}
