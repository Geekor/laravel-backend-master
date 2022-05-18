<?php

namespace Geekor\BackendMaster\Tests\Feature\Normal;

use Tests\TestCase; // 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Geekor\BackendMaster\Models\User;
use Geekor\Core\Support\GkApi;
use Geekor\Core\Support\GkTestUtil;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class NormalUserRegiserTest extends TestCase
{
    use WithFaker;

    const DEVICE_NAME = 'php-auto-test';
    const TESTING_API = '/api/auth/email-register';

    /*
    |--------------------------------------------------------------------------
    | 注册测试
    |--------------------------------------------------------------------------
    */

    /**
     * 注册 | 缺失 email （注册帐号）
     */
    public function test_register_without_email()
    {
        $resp = $this->postJson(self::TESTING_API, [
            'password' => 'password',
            'name' => Str::random(12)
        ]);

        $resp->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 注册 | 缺失 password
     */
    public function test_register_without_password()
    {
        $resp = $this->postJson(self::TESTING_API, [
            'email' => $this->faker->safeEmail(),
            'name' => Str::random(12)
        ]);

        $resp->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 注册 | 缺失 name
     */
    public function test_register_without_name()
    {
        $resp = $this->postJson(self::TESTING_API, [
            'email' => $this->faker->safeEmail(),
            'password' => 'password',
        ]);

        $resp->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 注册 | 错误的邮箱格式
     */
    public function test_register_with_bad_email_format()
    {
        $resp = $this->postJson(self::TESTING_API, [
            'email' => Str::random(10),
            'password' => Str::random(10),
            'name' => Str::random(10)
        ]);

        $resp->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_ERROR]);
    }

    /**
     * 注册成功
     */
    public function test_register_success()
    {
        $resp = $this->postJson(self::TESTING_API, [
            'email' => $this->faker->safeEmail(),
            'password' => 'password',
            'name' => Str::random(10)
        ]);

        $resp->assertCreated()->assertJsonStructure(['id', 'name', 'email']);
    }

}
