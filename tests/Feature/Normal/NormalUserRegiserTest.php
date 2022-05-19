<?php

namespace Geekor\BackendMaster\Tests\Feature\Normal;

use Geekor\BackendMaster\Models\User;
use Geekor\BackendMaster\Tests\Base\TestNormalCase;
use Geekor\Core\Support\GkApi;
use Geekor\Core\Support\GkTestUtil;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class NormalUserRegiserTest extends TestNormalCase
{
    use WithFaker;

    protected $my_testing_api = '/api/auth/email-register';

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
        $resp = $this->postJson($this->myTestingApi(), [
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
        $resp = $this->postJson($this->myTestingApi(), [
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
        $resp = $this->postJson($this->myTestingApi(), [
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
        $resp = $this->postJson($this->myTestingApi(), [
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
        $email = $this->faker->safeEmail();

        $resp = $this->postJson($this->myTestingApi(), [
            'email' => $email,
            'password' => 'password',
            'name' => Str::random(10)
        ]);

        $resp->assertCreated()->assertJsonStructure(['id', 'name', 'email']);

        User::where('email', $email)->delete();
    }

}
