<?php

namespace App\Modules\Identity\Database\Factories;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Mat khau hash 1 lan cho ca lo factory (nhanh hon khi sinh nhieu user).
     */
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('U#######')),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => UserStatus::Active,
            'must_change_password' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn () => ['status' => UserStatus::Locked]);
    }

    public function mustChangePassword(): static
    {
        return $this->state(fn () => ['must_change_password' => true]);
    }

    public function withRole(RoleName $role): static
    {
        return $this->afterCreating(function (User $user) use ($role) {
            Role::findOrCreate($role->value, 'web');
            $user->assignRole($role->value);
        });
    }

    public function admin(): static
    {
        return $this->withRole(RoleName::Admin);
    }

    public function lecturer(): static
    {
        return $this->withRole(RoleName::Lecturer);
    }

    public function student(): static
    {
        return $this->withRole(RoleName::Student);
    }
}
