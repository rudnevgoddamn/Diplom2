<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'nickname' => 'admin',
                'email' => 'a@mail.ru',
                'password' => bcrypt('admin'),
                'picture' => 'img/users/playerDefault.png',
                'role' => '2',
                'about' => 'Администратор и банхаммер сайта'

            ],
            [
                'nickname' => 'sskskskss',
                'email' => 's@mail.ru',
                'password' => bcrypt('oleksey21'),
                'picture' => 'img/users/playerDefault.png',
                'role' => '1',
                'about' => 'Пользователь еще не рассказал о себе'

            ],
            [
                'nickname' => 'gromozaur',
                'email' => 'g@mail.ru',
                'password' => bcrypt('oleksey21'),
                'picture' => 'img/users/playerDefault.png',
                'role' => '1',
                'about' => 'Пользователь еще не рассказал о себе'

            ],
        ];
        DB::table('users')->insert($users);
    }
}
