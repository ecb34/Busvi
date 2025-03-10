<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\Event;

class ResetSuperadminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset_superadmin_password {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reestablecer contraseña de superadmin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $password = $this->argument('password');
        $superadmin = \App\User::where('username', 'superadmin')->first();

        if(is_null($superadmin)){
            
            $superadmin = new \App\User; 
            $superadmin->name = 'SuperAdmin';
            $superadmin->username = 'superadmin';
            $superadmin->email = 'superadmin@microvalencia.es';
            $superadmin->password = bcrypt($password);
            $superadmin->role = 'superadmin';
            $superadmin->save();

            $this->info(' - Usuario superadmin creado con la nueva contraseña');

        } else {

            $superadmin->password = bcrypt($password);
            $superadmin->role = 'superadmin';
            $superadmin->save();

            $this->info(' - Usuario superadmin actualizado con la nueva contraseña');

        }
        
    }
}
