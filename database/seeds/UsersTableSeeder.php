<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        factory(User::class, 50)->create()->each(function ($user) {
            $user->attachRole('user');
        });

        factory(User::class, 1)->create([
            'email' => config('mail.from')['address'],
            'password' => Hash::make('secret1!')
        ])->first()->attachRole('superadministrator');
    }
}
