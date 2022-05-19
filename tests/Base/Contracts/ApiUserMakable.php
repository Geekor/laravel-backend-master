<?php

namespace Geekor\BackendMaster\Tests\Base\Contracts;

interface ApiUserMakable
{
    public function makeMasterUserAndToken($usePermission = false): array;
    public function makeNormalUserAndToken($usePermission = false): array;
}
