<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Tests\Base\TestAuthCase;
use Geekor\Core\Support\GkApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
class MasterLoginTest extends TestAuthCase
{
    protected $my_testing_api = '/api/backend/auth/login';

    /*
    |--------------------------------------------------------------------------
    | 登录测试
    |--------------------------------------------------------------------------
    */

    /**
     * 登录 | 缺失 username
     */
    public function test_login_without_username()
    {
        $response = $this->postJson($this->myTestingApi(), [
            'password' => 'password',
            'device_name' => $this->myDeviceName()
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 缺失 password
     */
    public function test_login_without_password()
    {
        $response = $this->postJson($this->myTestingApi(), [
            'username' => $this->faker->userName(),
            'device_name' => $this->myDeviceName()
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 缺失 device_name
     */
    public function test_login_without_device_name()
    {
        $response = $this->postJson($this->myTestingApi(), [
            'username' => $this->faker->userName(),
            'password' => 'password',
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 错误的密码
     */
    public function test_login_with_bad_password()
    {
        $user = Master::factory()->create();

        $response = $this->postJson($this->myTestingApi(), [
            'username' => $user->username,
            'password' => Str::random(10),
            'device_name' => $this->myDeviceName()
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_ERROR]);

        $user->delete();
    }

    /**
     * 登录成功
     */
    public function test_login_success()
    {
        $user = Master::factory()->create();

        $response = $this->postJson($this->myTestingApi(), [
            'username' => $user->username,
            'password' => 'password',
            'device_name' => $this->myDeviceName()
        ]);

        $response->assertOk()->assertJsonStructure(['info', 'token']);

        $user->delete();
    }

}
