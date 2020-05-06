<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
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
        $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/projects', [
            'team_id' => $team->id,
            'relations' => 'users, team, languages, keys',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'users',
                        'team',
                        'languages',
                        'keys',
                    ],
                ],
            ])
            ->assertJson([
                'data' => $team->projects->toArray(),
            ]);

        $this->assertCount(1, $team->refresh()->projects);
    }

    /**
     * @return void
     */
    public function testShow()
    {
        $team = $this->user->teams()->save(factory(Team::class)->make());
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/projects/'.$project->id, [
            'relations' => 'users, team, languages, keys',
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'users',
                    'team',
                    'languages',
                    'keys',
                ],
            ])
            ->assertJson([
                'data' => $project->toArray(),
            ]);

        $this->assertCount(1, $team->refresh()->projects);
    }
}
