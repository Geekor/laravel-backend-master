<?php

namespace Geekor\BackendMaster\Console\Commands;

use Illuminate\Console\Command;

class Check extends Command
{
    protected $signature = 'bm:check';
    protected $description = 'Geekor Backend Master state check';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->newLine();
        $this->line(' # '.$this->signature);
        $this->newLine();

        var_dump(config('auth.providers'));

        $this->info(' hi, logico! ');
    }
}
