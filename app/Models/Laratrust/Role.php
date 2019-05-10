<?php

namespace App\Models\Laratrust;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $fillable = ['name', 'display_name', 'description'];
}
