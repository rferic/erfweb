<?php

namespace App\Models\Core;

use App\Http\Helpers\LocalizationHelper;
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
        'name', 'email', 'password', 'avatar', 'lang',
    ];

    protected $appends = [ 'language' ];

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

    public function isAdmin ()
    {
        return $this->hasRole('superadministrator') || $this->hasRole('administrator');
    }

    public function isBanned ()
    {
        return $this->trashed();
    }

    public function apps ()
    {
        return $this->belongsToMany(App::class)->using(AppUser::class)->withTimestamps()->withPivot('active');
    }

    public function comments ()
    {
        return $this->hasMany(Comment::class);
    }

    public function messages ()
    {
        return $this->hasMany(Message::class, 'author_id');
    }

    public function getLanguageAttribute ()
    {
        $localesSupported = LocalizationHelper::getSupportedFormatted();

        foreach ( $localesSupported AS $localeSupported ) {
            if ( $localeSupported['iso'] === $this->lang ) {
                return $localeSupported;
            }
        }

        return null;
    }

    public function messagesReceived ()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function locale ()
    {
        $localesSupported = LocalizationHelper::getSupportedFormatted();

        foreach ( $localesSupported AS $localeSupported ) {
            if ( $localeSupported['iso'] === $this->lang ) {
                return $localeSupported['code'];
            }
        }

        return null;
    }

    public function MessageReceived ()
    {
        $this->notify(new Notification)->locale($this->locale());
    }

    public function MessageDeleted ()
    {
        $this->notify(new Notification)->locale($this->locale());
    }

    public function AppUserRegistered ()
    {
        $this->notify(new Notification)->locale($this->locale());
    }

    public function AppUserAccepted ()
    {
        $this->notify(new Notification)->locale($this->locale());
    }

    public function AppUserBanned ()
    {
        $this->notify(new Notification)->locale($this->locale());
    }
}
