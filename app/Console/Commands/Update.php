<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\LocaleRepository;
use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;
use App\Repositories\EmailTemplateRepository;

class Update extends Command
{
    protected $locale;
    protected $role;
    protected $permission;
    protected $email_template;

    /**
     *  This command is used to reset the application to factory condition.
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh Updateation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        LocaleRepository $locale,
        RoleRepository $role,
        PermissionRepository $permission,
        EmailTemplateRepository $email_template
    ) {
        $this->locale         = $locale;
        $this->role           = $role;
        $this->permission     = $permission;
        $this->email_template = $email_template;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(1);


        // Create default permissions
        $system_variables = getVar('system');
        config(['system' => $system_variables]);
        $permissions = $this->permission->listName();
        foreach (config('system.default_permission') as $value) {
            if (!in_array($value, $permissions)) {
                Permission::create(['name' => strtolower($value)]);
            }
        }

        $bar->advance();

        // Assign default permission to admin roles

        $role = Role::whereName(config('system.default_role.admin'))->first();
        $role->syncPermissions(config('system.default_permission'));
    

        $bar->finish();
    }
}
