<?php

namespace Geekor\BackendMaster\Console\Commands;

use Geekor\Core\Support\GkFileContent;
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

        // var_dump(config('auth.providers'));

        $file = base_path('app/Http/Kernel.php');
        $content = 'use \Geekor\BackendMaster\Traits\SettingRoutes;';
        $find = 'class Kernel extends HttpKernel';
        $offset = 2;
        if (! GkFileContent::hasContent($file, $content)) {
            GkFileContent::insertAfterTarget($file, $content, $find, $offset);
        }

        $file = base_path('app/Exceptions/Handler.php');
        $content = 'use \Geekor\BackendMaster\Traits\ExceptionHandler;';
        $find = 'class Handler extends ExceptionHandler';
        $offset = 2;
        if (! GkFileContent::hasContent($file, $content)) {
            GkFileContent::insertAfterTarget($file, $content, $find, $offset);
        }

        // $this->info(' hi, logico! ');
    }
}
