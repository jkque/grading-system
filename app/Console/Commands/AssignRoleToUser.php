<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class AssignRoleToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:assign-role {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to user';

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
        $role = $this->ask('What is your role?');
        $user = User::whereEmail($this->argument('email'))->first();
        $user->assignRole($role);
        $this->info("{$role} role has been assigned to {$user->name}");
    }
}
