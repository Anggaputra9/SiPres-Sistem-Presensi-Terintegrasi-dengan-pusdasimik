<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->numerify('##########'),
            'role' => User::ROLE_MAHASISWA,
            'nama' => fake()->name(),
            'program_studi' => fake()->randomElement(['Informatika', 'Sistem Informasi']),
            'fakultas' => 'Teknik',
            'jabatan' => null,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Create a lecturer account.
     */
    public function dosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_DOSEN,
            'program_studi' => null,
            'fakultas' => null,
            'jabatan' => 'Dosen',
        ]);
    }
}
