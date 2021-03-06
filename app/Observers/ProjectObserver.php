<?php

namespace App\Observers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectObserver
{
    /**
     * Handle the project "created" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        $project->users()->attach(Auth::user(), ['is_owner' => true]);

        $token = $project->createToken('', []);

        $project->setting()->create([
            'settings' => [
                'api_key' => $token->plainTextToken,
            ],
        ]);
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        $project->users()->detach();
        $project->languages()->detach();
    }
}
