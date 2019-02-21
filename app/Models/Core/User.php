<?php

namespace App\Models\Core;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static function boot ()
    {
        parent::boot();

        static::deleting (function ($user) {
            if ($user->forceDeleting && !App::runningInConsole()) {
                $user->comments()->forceDelete();
                $user->messages()->forceDelete();
                $user->apps()->detach();
            }
        });
    }

    public function isMe ()
    {
        return $this->id === auth()->id();
    }

    public function isBanned ()
    {
        return $this->trashed();
    }

    public function apps ()
    {
        return $this->belongsToMany(App::class)->withTimestamps();
    }

    public function comments ()
    {
        return $this->hasMany(Comment::class);
    }

    public function messages ()
    {
        return $this->hasMany(Message::class, 'author_id');
    }

    public function messagesReceived ()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
