<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usertype;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usertype>
 */
class UsertypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Usertype::class;
    public function definition(): array
    {
         return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
