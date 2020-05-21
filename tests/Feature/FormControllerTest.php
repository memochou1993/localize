<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testShow()
    {
        $user = Sanctum::actingAs($this->user, ['view-form']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('GET', 'api/forms/'.$form->id, [
            'relations' => '',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ])
            ->assertJson([
                'data' => $form->toArray(),
            ]);
    }

    /**
     * @return void
     */
    public function testUpdate()
    {
        $user = Sanctum::actingAs($this->user, ['update-form']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $data = factory(Form::class)->make([
            'name' => 'New Form',
        ])->toArray();

        $this->json('PATCH', 'api/forms/'.$form->id, $data)
            ->assertOk()
            ->assertJson([
                'data' => $data,
            ]);

        $this->assertDatabaseHas('forms', $data);
    }

    /**
     * @return void
     */
    public function testUpdateDuplicate()
    {
        $user = Sanctum::actingAs($this->user, ['update-form']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $forms = $team->forms()->saveMany(factory(Form::class, 2)->make());

        $data = factory(Form::class)->make([
            'name' => $forms->last()->name,
        ])->toArray();

        $this->json('PATCH', 'api/forms/'.$forms->first()->id, $data)
            ->assertJsonValidationErrors([
                'name',
            ]);
    }

    /**
     * @return void
     */
    public function testDestroy()
    {
        $user = Sanctum::actingAs($this->user, ['delete-form']);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertNoContent();

        $this->assertDeleted($form);
    }

    /**
     * @return void
     */
    public function testGuestView()
    {
        Sanctum::actingAs($this->user, ['view-form']);

        $team = factory(Team::class)->create();
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('GET', 'api/forms/'.$form->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestUpdate()
    {
        Sanctum::actingAs($this->user, ['update-form']);

        $team = factory(Team::class)->create();
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('PATCH', 'api/forms/'.$form->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testGuestDelete()
    {
        Sanctum::actingAs($this->user, ['delete-form']);

        $team = factory(Team::class)->create();
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testViewWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('GET', 'api/forms/'.$form->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testUpdateWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('PATCH', 'api/forms/'.$form->id)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function testDeleteWithoutPermission()
    {
        $user = Sanctum::actingAs($this->user);

        $team = $user->teams()->save(factory(Team::class)->make());
        $form = $team->forms()->save(factory(Form::class)->make());

        $this->json('DELETE', 'api/forms/'.$form->id)
            ->assertForbidden();
    }
}
