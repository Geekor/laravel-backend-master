<?php

namespace Geekor\BackendMaster\Tests\Base;

interface ApiTestable
{
    public function myFaker(): \Faker\Generator;

    public function makeMasterUserAndToken($usePermission = false): array;
    public function makeNormalUserAndToken($usePermission = false): array;

    public function isMasterGuard(): bool;
    public function isPermissionRequired(): bool;
    public function myDeviceName(): string;
    public function myTestingApi(): string;
    public function myTestingMethod(): string;
    public function myTestingParams(): array;

}
