<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Spatie\Permission\Models\Role;

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

        Role::create(['name' => 'public']);
        Role::create(['name' => 'admin']);

        factory(User::class, 5)->create()->each(function ($user) {
            $user->assignRole('public');
        });

        factory(User::class, 1)->create([
            'email' => config('mail.from')['address'],
            'password' => Hash::make('secret1!')
        ])->first()->assignRole('admin');
    }
}
