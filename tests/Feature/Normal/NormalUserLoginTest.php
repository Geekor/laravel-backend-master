<?php

namespace Geekor\BackendMaster\Tests\Feature\Normal;

use Geekor\BackendMaster\Models\User;
use Geekor\BackendMaster\Tests\Base\TestNormalCase;
use Geekor\Core\Support\GkApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class NormalUserLoginTest extends TestNormalCase
{
    use WithFaker;

    protected $my_testing_api = '/api/auth/email-login';

    /*
    |--------------------------------------------------------------------------
    | 登录测试
    |--------------------------------------------------------------------------
    */

    /**
     * 登录 | 缺失 email （登录帐号）
     */
    public function test_login_without_email()
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
            'email' => $this->faker->safeEmail(),
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
            'email' => $this->faker->safeEmail(),
            'password' => 'password',
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 错误的密码
     */
    public function test_login_with_bad_password()
    {
        $user = User::factory()->create();

        $response = $this->postJson($this->myTestingApi(), [
            'email' => $user->email,
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
        $user = User::factory()->create();

        $response = $this->postJson($this->myTestingApi(), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => $this->myDeviceName()
        ]);

        $response->assertOk()->assertJsonStructure(['info', 'token']);

        $user->delete();
    }

}
