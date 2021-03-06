<?php

namespace App\Models\Core;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\App as Application;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use LaratrustUserTrait;
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
            if ($user->forceDeleting && !Application::runningInConsole()) {
                $user->comments()->forceDelete();
                $user->messages()->forceDelete();
                $user->messagesReceived()->forceDelete();
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
        return $this->belongsToMany(App::class)->withTimestamps()->withPivot('active');
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

    public function MessageReceivedNotification ()
    {
        $this->notify(new Notification);
    }

    public function MessageDeletedNotification ()
    {
        $this->notify(new Notification);
    }
}
