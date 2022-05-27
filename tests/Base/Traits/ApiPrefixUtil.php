<?php

namespace Geekor\BackendMaster\Tests\Base\Traits;

use Illuminate\Support\Str;

trait ApiPrefixUtil
{
    protected function fixApiBackendPrefix()
    {
        // $bm = Collection::make($this->app->configPath().DIRECTORY_SEPARATOR.'bm.php');
        if (empty($this->config)) {
            $this->config = resolve('config'); // resolve() == $app->make()
        }

        if ($this->config['bm.prefix'] === 'backend') {
            return;
        } else if (empty($this->my_testing_api )) {
            return;
        } else if (! Str::startsWith($this->my_testing_api, '/api/backend/')) {
            return;
        }

        $prefix = vsprintf('/api/%s/', [$this->config['bm.prefix']]);

        $this->my_testing_api = Str::replaceFirst('/api/backend/', $prefix, $this->my_testing_api);

        // var_dump('...fix prefix: ' . $this->my_testing_api );
    }
}
