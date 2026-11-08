<?php

namespace Tests\Feature\Identity;

use App\Modules\Identity\Models\User;
use App\Shared\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\CreatesRoles;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use CreatesRoles, RefreshDatabase;

    /**
     * @return array<string, array{string, RoleName, int}>
     */
    public static function matrix(): array
    {
        $cases = [];

        $expected = [
            '/admin' => RoleName::Admin,
            '/lecturer' => RoleName::Lecturer,
            '/student' => RoleName::Student,
        ];

        foreach ($expected as $uri => $allowed) {
            foreach (RoleName::cases() as $role) {
                $cases["{$role->value} -> {$uri}"] = [$uri, $role, $role === $allowed ? 200 : 403];
            }
        }

        return $cases;
    }

    #[DataProvider('matrix')]
    public function test_route_access_per_role(string $uri, RoleName $role, int $status): void
    {
        $this->seedRoles();

        $user = User::factory()->withRole($role)->create();

        $this->actingAs($user)->get($uri)->assertStatus($status);
    }

    public function test_profile_route_is_available_to_every_role(): void
    {
        $this->seedRoles();

        foreach (RoleName::cases() as $role) {
            $user = User::factory()->withRole($role)->create();

            $this->actingAs($user)->get('/profile')->assertOk();
        }
    }
}
