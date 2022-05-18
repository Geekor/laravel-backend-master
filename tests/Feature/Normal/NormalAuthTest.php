<?php

namespace Geekor\BackendMaster\Tests\Feature\Normal;

use Tests\TestCase; // 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Geekor\BackendMaster\Models\User;
use Geekor\Core\Support\GkApi;
use Geekor\Core\Support\GkTestUtil;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class NormalAuthTest extends TestCase
{
    use WithFaker;

    const DEVICE_NAME = 'php-auto-test';
    const TESTING_API = '/api/auth/email-login';

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
        $response = $this->postJson(self::TESTING_API, [
            'password' => 'password',
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 缺失 password
     */
    public function test_login_without_password()
    {
        $response = $this->postJson(self::TESTING_API, [
            'email' => $this->faker->safeEmail(),
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 缺失 device_name
     */
    public function test_login_without_device_name()
    {
        $response = $this->postJson(self::TESTING_API, [
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

        $response = $this->postJson(self::TESTING_API, [
            'email' => $user->email,
            'password' => Str::random(10),
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_ERROR]);
    }

    /**
     * 登录成功
     */
    public function test_login_success()
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::TESTING_API, [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertOk()->assertJsonStructure(['info', 'token']);
    }

}
