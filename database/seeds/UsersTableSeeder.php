<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\User; 
        $user->name = 'SuperAdmin';
        $user->username = 'superadmin';
        $user->email = 'superadmin@microvalencia.es';
        $user->password = bcrypt('Mvlc7931');
        $user->role = 'superadmin';
        $user->save();

        $user = new \App\User; 
        $user->name = 'SuperAdmin2';
        $user->username = 'superadmin2';
        $user->email = 'info@freshware.es';
        $user->password = bcrypt('12345');
        $user->role = 'superadmin';
        $user->save();

        $user = new \App\User; 
        $user->name = 'Operadora';
        $user->username = 'operadora1';
        $user->email = 'mail@operadora.com';
        $user->password = bcrypt('123123');
        $user->role = 'operator';
        $user->save();
    }
}
