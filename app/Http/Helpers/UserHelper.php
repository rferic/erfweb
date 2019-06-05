<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 14/11/2018
 * Time: 21:22
 */

namespace App\Http\Helpers;

use App\Models\Core\User;
use Illuminate\Http\Request;
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

    static function refreshRoles ( User $user, $roles)
    {
        foreach ( $roles AS $role ) {
            if ( $user->hasRole($role['key']) !== boolval($role['value']) ) {
                if ( $role['value'] ) {
                    $user->attachRole($role['key']);
                } else {
                    $user->detachRole($role['key']);
                }
            }
        }
    }

    static function getAvatars () {
        return Storage::disk('public')->files(config('global.pathAvatars'));
    }

    static function storeAvatar ( User $user, Request $request )
    {
        $image = $request->input('avatar');
        // Save user
        $avatars = self::getAvatars();
        // If avatar is a temporal image move to static folder
        if ( !in_array($image, $avatars) && $image !== $user->avatar ) {
            $imagePaths = self::getImagePaths($user, $image);
            $image = ImageHelper::storeImageTemporal($imagePaths['tmp'], $imagePaths['new']);
        }
        // Remove old image if is required
        if ( !in_array($user->avatar, $avatars) && $image !== $user->avatar ) {
            ImageHelper::removeImageTemporal(str_replace('storage/', '', $user->avatar));
        }

        return $image;
    }

    static function getImagePaths ( User $user, $image )
    {
        $imageTmp = str_replace('storage/', '', $image);

        return [
            'tmp' => $imageTmp,
            'new' => str_replace(ImageHelper::$temporalPath, 'images/users/' . $user->id . '/avatar', $imageTmp)
        ];
    }
}
