<?php

namespace Geekor\BackendMaster\Tests\Base\Contracts;

interface ApiTestable
{
    public function isMasterGuard(): bool;
    public function isPermissionRequired(): bool;
    public function myDeviceName(): string;
    public function myTestingApi(): string;
    public function myTestingMethod(): string;
    public function myTestingParams(): array;

}
