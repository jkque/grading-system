<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class GenerateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:generate-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate roles for users';

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
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'school_admin']);
        Role::create(['name' => 'guardian']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'student']);
        $this->info('Roles have been created.');
    }
}
