<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 5, 500),
            'description' => fake()->sentence(10),
            'category' => fake()->randomElement(['electronics', 'clothing', 'books', 'sports', 'home']),
            'image' => fake()->imageUrl(640, 480, 'products', true),
            'rating' => [
                'rate' => fake()->randomFloat(1, 1, 5),
                'count' => fake()->numberBetween(1, 1000)
            ],
            'external_id' => fake()->unique()->numberBetween(1, 999999),
        ];
    }

    /**
     * Create a product with a specific price.
     */
    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }

    /**
     * Create a product in a specific category.
     */
    public function inCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Create a product with a specific title.
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
        ]);
    }
}
