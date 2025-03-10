<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /*  $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'Ver Usuarios']);
        $permission = Permission::create(['name' => 'Editar Usuarios']);
        $role->syncPermissions('Ver Usuarios','Editar Usuarios');       */

        $role = Role::findByName('superadmin');
        $role->syncPermissions('Ver Usuarios','Editar Usuarios');

        $role = Role::findByName('admin');
        $role->syncPermissions('Ver Usuarios','Editar Usuarios');
    }
}
