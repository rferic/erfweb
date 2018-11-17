<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Spatie\Permission\Models\Role;
use App\Http\Helpers\RoleHelper;

use App\Models\Core\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');

        foreach ( RoleHelper::getRoles() AS $role ) {
            Role::create(['name' => $role]);
        }

        factory(User::class, 5)->create()->each(function ($user) {
            $user->assignRole('public');
        });

        factory(User::class, 1)->create([
            'email' => config('mail.from')['address'],
            'password' => Hash::make('secret1!')
        ])->first()->assignRole('admin');
    }
}
