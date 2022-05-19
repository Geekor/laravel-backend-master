<?php

namespace Geekor\BackendMaster\Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Geekor\BackendMaster\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \Geekor\BackendMaster\Models\User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $email = '';
        do {
            try {
                $email = $this->faker->unique()->safeEmail();
                break;
            } catch (Exception $e) {}
        } while(true);

        return [
            'name' => $this->faker->name(),
            'email' => $email,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
