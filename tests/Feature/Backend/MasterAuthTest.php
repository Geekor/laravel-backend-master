<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Tests\TestCase; // 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Geekor\BackendMaster\Models\Master;
use Geekor\Core\Support\GkApi;
use Geekor\Core\Support\GkTestUtil;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MasterAuthTest extends TestCase
{
    use WithFaker;

    const DEVICE_NAME = 'php-auto-test';

    /**
     * 访问一个不存在的 API
     */
    public function test_try_a_not_exists_api()
    {
        $this->get('/api/just-not-exists')->assertNotFound();
    }

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
        $this->faker->userName();

        $response = $this->post('/api/backend/auth/login', [
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
        $this->faker->userName();

        $response = $this->post('/api/backend/auth/login', [
            'username' => $this->faker->userName(),
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_MISS]);
    }

    /**
     * 登录 | 缺失 device_name
     */
    public function test_login_without_device_name()
    {
        $this->faker->userName();

        $response = $this->post('/api/backend/auth/login', [
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
        $master = Master::factory()->create();

        $response = $this->post('/api/backend/auth/login', [
            'username' => $master->username,
            'password' => 'qsdfwe',
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertStatus(400)->assertJson(['code' => GkApi::API_PARAM_ERROR]);
    }

    /**
     * 登录成功
     */
    public function test_login_success()
    {
        $master = Master::factory()->create();

        $response = $this->post('/api/backend/auth/login', [
            'username' => $master->username,
            'password' => 'password',
            'device_name' => self::DEVICE_NAME
        ]);

        $response->assertOk()->assertJsonStructure(['info', 'token']);
    }

}
