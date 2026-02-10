<?php

namespace Database\Factories;

use App\Enums\PhoneType;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phone>
 */
class PhoneFactory extends Factory
{
    protected $model = Phone::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'country_code' => '+55',
            'number' => fake()->numerify('##9########'),
            'type' => fake()->randomElement([PhoneType::MOBILE, PhoneType::HOME, PhoneType::WORK]),
            'is_primary' => false,
        ];
    }

    /**
     * Indicate that the phone is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the phone is mobile.
     */
    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PhoneType::MOBILE,
        ]);
    }
}

