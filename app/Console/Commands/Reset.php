<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class Reset extends Command
{
    /**
     *  This command is used to reset the application to factory condition.
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Installation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->reset();
    }

    /**
     * Clean database
     * @return void
     */
    private function reset()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tableNames as $name) {
            if ($name != 'migrations') {
                DB::table($name)->truncate();
            }
        }

        DB::unprepared(file_get_contents(database_path('reset.sql')));

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
