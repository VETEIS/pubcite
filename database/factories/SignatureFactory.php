<?php

namespace Database\Factories;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Signature>
 */
class SignatureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Signature::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => $this->faker->optional()->words(2, true),
            'path' => 'signatures/' . $this->faker->numberBetween(1, 100) . '/' . $this->faker->uuid . '.png',
            'mime_type' => 'image/png',
        ];
    }
}
