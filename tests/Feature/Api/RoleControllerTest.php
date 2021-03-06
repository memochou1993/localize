<?php

namespace Tests\Feature\Api;

use App\Enums\ErrorType;
use App\Enums\PermissionType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::ROLE_VIEW_ANY,
        ]);

        Role::factory()->create();

        $this->json('GET', 'api/roles', [
            'relations' => 'users,permissions',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'permissions',
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::ROLE_CREATE,
        ]);

        /** @var Permission $permission */
        $permission = Permission::factory()->create();

        $data = Role::factory()->make([
            'permission_ids' => $permission->id,
        ])->toArray();

        $response = $this->json('POST', 'api/roles', $data)
            ->assertCreated();

        /** @var Role $role */
        $role = Role::query()->find(json_decode($response->getContent())->data->id);

        $this->assertCount(1, $role->refresh()->permissions);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::ROLE_CREATE,
        ]);

        Role::factory()->create([
            'name' => 'Unique Role',
        ]);

        $data = Role::factory()->make([
            'name' => 'Unique Role',
        ])->toArray();

        $this->json('POST', 'api/roles', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::ROLE_VIEW,
        ]);

        /** @var Role $role */
        $role = Role::factory()->create();

        $this->json('GET', 'api/roles/'.$role->id, [
            'relations' => 'users,permissions',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'permissions',
                ],
            ])
            ->assertJson([
                'data' => $role->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::ROLE_UPDATE,
        ]);

        /** @var Permission $permission */
        $permission = Permission::factory()->create();

        /** @var Role $role */
        $role = Role::factory()->create();

        $data = Role::factory()->make([
            'name' => 'New Role',
            'permission_ids' => $permission->id,
        ])->toArray();

        $this->json('PATCH', 'api/roles/'.$role->id, $data)
            ->assertOk();

        $this->assertCount(1, $role->refresh()->permissions);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        Sanctum::actingAs($this->user, [
            PermissionType::ROLE_UPDATE,
        ]);

        /** @var Role $role */
        $role = Role::factory()->create();

        $data = Role::factory()->create()->toArray();

        $this->json('PATCH', 'api/roles/'.$role->id, $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        /** @var User $user */
        $user = Sanctum::actingAs($this->user, [
            PermissionType::ROLE_DELETE,
        ]);

        /** @var Role $role */
        $role = $user->roles()->save(Role::factory()->make());

        $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertNoContent();

        $this->assertDeleted($role);

        $this->assertDatabaseMissing('model_has_users', [
            'user_id' => $user->id,
            'model_type' => 'role',
            'model_id' => $role->id,
        ]);
    }

    /**
     * @return void
     */
    public function testViewAllWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('GET', 'api/roles')
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testCreateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        $data = Role::factory()->make()->toArray();

        $response = $this->json('POST', 'api/roles', $data)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Role $role */
        $role = Role::factory()->create();

        $response = $this->json('GET', 'api/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Role $role */
        $role = Role::factory()->create();

        $response = $this->json('PATCH', 'api/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        Sanctum::actingAs($this->user);

        /** @var Role $role */
        $role = Role::factory()->create();

        $response = $this->json('DELETE', 'api/roles/'.$role->id)
            ->assertForbidden();

        $this->assertEquals(
            ErrorType::PERMISSION_DENIED,
            $response->exception->getCode()
        );
    }
}
