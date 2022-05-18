<?php

namespace Geekor\BackendMaster\Tests\Base;

interface ApiTestable
{
    public function isMasterGuard(): bool;
    public function myDeviceName(): string;
    public function myTestingApi(): string;
    public function myTestingMethod(): string;
    public function myTestingParams(): array;

}
