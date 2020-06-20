<?php

namespace Tests\Feature\Api\Client;

use App\Models\Form;
use App\Models\Key;
use App\Models\Language;
use App\Models\Project;
use App\Models\Team;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testShow()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        /** @var Language $language */
        $language = $team->languages()->save(factory(Language::class)->make());
        $project->languages()->attach($language);

        /** @var Form $form */
        $form = $team->forms()->save(factory(Form::class)->make());
        $language->forms()->attach($form);

        /** @var Key $key */
        $key = $project->keys()->save(factory(Key::class)->make());

        /** @var Value $value */
        $key->values()->save(factory(Value::class)->make());

        /** @var Value $value */
        $value = $key->values()->save(factory(Value::class)->make());
        $value->languages()->attach($language);
        $value->forms()->attach($form);

        $this
            ->withHeaders([
                'X-Lexicon-API-Key' => $project->getSetting('api_key'),
            ])
            ->json('GET', 'api/client/projects/'.$project->id)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'languages',
                    'keys' => [
                        [
                            'values' => [
                                [
                                    'language',
                                    'form',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testUnauthorized()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var Project $project */
        $project = $team->projects()->save(factory(Project::class)->make());

        $this->json('GET', 'api/client/projects/'.$project->id)
            ->assertUnauthorized();
    }
}
