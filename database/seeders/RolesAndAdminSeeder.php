<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['visitor','hotel_manager','ferry_staff','theme_staff','admin'] as $r) {
            Role::findOrCreate($r);
        }

        // Create baseline users per role for testing/demo
        $demoUsers = [
            'admin@picnic.test' => ['Admin','admin'],
            'manager@picnic.test' => ['Hotel Manager','hotel_manager'],
            'ferry@picnic.test' => ['Ferry Staff','ferry_staff'],
            'theme@picnic.test' => ['Theme Park Staff','theme_staff'],
            'visitor@picnic.test' => ['Demo Visitor','visitor'],
        ];
        foreach ($demoUsers as $email => [$name,$role]) {
            if (!User::where('email',$email)->exists()) {
                $u = User::create(['name'=>$name,'email'=>$email,'password'=>Hash::make('password')]);
                $u->assignRole($role);
            }
        }
    }
}
