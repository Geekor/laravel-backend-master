<?php

namespace Geekor\BackendMaster\Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Geekor\BackendMaster\Models\Master>
 */
class MasterFactory extends Factory
{
    protected $model = \Geekor\BackendMaster\Models\Master::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $username = $this->faker->unique()->userName();

        return [
            'name' => $this->faker->name(),
            'username' => $username,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function create($attributes = [], ?Model $parent = null)
    {
        $results = null;

        do { try {
            $results = parent::create($attributes, $parent);
            break;
        } catch (Exception $e) {}} while(true);

        return $results;
    }
}
