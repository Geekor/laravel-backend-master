<?php

namespace Geekor\BackendMaster\Console\Commands;

use Illuminate\Console\Command;

class MakeApiDoc extends Command
{
    protected $signature = 'bm:make-apidoc';
    protected $description = 'Generate backend master apidoc';

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
        $this->line(' ');
        $this->line(' # '.$this->signature);
        $this->line(' ');

        // var_dump(config('auth.providers'));
        // var_dump(__DIR__);
        // var_dump(public_path('apidoc'));
        // var_dump(public_path('storage'));

        $apidoc_path = public_path('apidoc/v1');
        if (! is_dir($apidoc_path)) {
            mkdir($apidoc_path, 0644, true);

            if (! is_dir($apidoc_path)) {
                $this->error(' >>> 创建 apidoc/v1 目录失败，你需要在 public 目录手动创建它');
                return 1;
            }
        }

        $ret = shell_exec('apidoc --version');
        if ($ret === null) {
            $this->error(' >>> apidoc 命令不存在，请使用 sudo npm i -g apidoc 安装');
            return 2;
        }

        $src_dir = __DIR__.'/../../../docs/v1';
        $cfg = __DIR__.'/../../../docs/v1/apidoc.json';

        $cmd = vsprintf('apidoc -c %s -i %s -o %s', [$cfg, $src_dir, $apidoc_path]);
        // var_dump($cmd);
        echo shell_exec($cmd);
    }
}
