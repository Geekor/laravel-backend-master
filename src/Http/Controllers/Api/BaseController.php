<?php

namespace Geekor\BackendMaster\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as CoreController;

use Geekor\BackendMaster\Http\Traits\PermissionCheck;
use Geekor\BackendMaster\Http\Traits\InputCheck;

class BaseController extends CoreController
{
    use InputCheck, PermissionCheck; // BM
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
