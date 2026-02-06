<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'description' => 'ادمین کل'],
            ['name' => 'manager', 'description' => 'مدیر'],
            ['name' => 'inspector_general', 'description' => 'سربازرس'],
            ['name' => 'inspector', 'description' => 'بازرس'],
            ['name' => 'staff', 'description' => 'نیروی اداری'],
            ['name' => 'line_supervisor', 'description' => 'ناظر خط'],
        ];

        DB::table('roles')->insert($roles);
    }
}
