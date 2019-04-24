<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 14/11/2018
 * Time: 21:22
 */

namespace App\Http\Helpers;

use App\Models\Core\User;
use Illuminate\Support\Facades\Storage;

class UserHelper
{
    static function getRolesAssignToUser ( User $user )
    {
        $rolesAssign = [];
        $roles = RoleHelper::getRoles();

        foreach ( $roles AS $role ) {
            if ( $user->hasRole($role) ) {
                array_push($rolesAssign, $role);
            }
        }

        return $rolesAssign;
    }

    static function emailIsFree ( User $user, $email )
    {
        $user2 = User::where('email', $email)->first();
        return is_null($user2) || (isset($user2->id) && $user2->id === $user->id);
    }

    static function refreshRoles ( User $user, $roles)
    {
        foreach ( $roles AS $role ) {
            if ( $user->hasRole($role['key']) !== boolval($role['value']) ) {
                if ( $role['value'] ) {
                    $user->assignRole($role['key']);
                } else {
                    $user->removeRole($role['key']);
                }
            }
        }
    }

    static function getAvatars () {
        return Storage::disk('public')->files(config('global.pathAvatars'));
    }
}
