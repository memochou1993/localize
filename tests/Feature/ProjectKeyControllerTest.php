<?php

namespace Tests\Feature;

use App\Models\Key;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectKeyControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = Sanctum::actingAs(factory(User::class)->create());
    }

    /**
     * @return void
     */
    public function testIndex()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make());

        $this->json('GET', 'api/projects/'.$project->id.'/keys', [
            'relations' => 'values',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'values',
                    ],
                ],
            ])
            ->assertJson([
                'data' => $project->keys->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testStore()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $data = factory(Key::class)->make()->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('keys', $data);

        $this->assertCount(1, $project->keys);
    }

    /**
     * @return void
     */
    public function testStoreDuplicate()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());
        $project->keys()->save(factory(Key::class)->make([
            'name' => 'Unique Key',
        ]));

        $data = factory(Key::class)->make([
            'name' => 'Unique Key',
        ])->toArray();

        $this->json('POST', 'api/projects/'.$project->id.'/keys', $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => [
                    'name',
                ],
            ]);

        $this->assertCount(1, $project->keys);
    }
}