<?php

namespace App\Observers;

use App\Models\Core\AppUser;
use App\Models\Core\User;
use App\Notifications\AppUserAccepted;
use App\Notifications\AppUserBanned;
use App\Notifications\AppUserRegistered;

class AppUserObserver
{
    /**
     * Handle the appUser "created" event.
     *
     * @param  \App\AppUser  $item
     * @return void
     */
    public function created ( AppUser $item )
    {
        User::whereRoleIs('superadministrator')->first()->notify(new AppUserRegistered($item));
    }

    /**
     * Handle the appuser "updated" event.
     *
     * @param  \App\AppUser  $item
     * @return void
     */
    public function updated ( AppUser $item )
    {
        $item->user->notify($item->active ? new AppUserAccepted($item) : new AppUserBanned($item));
    }
}
