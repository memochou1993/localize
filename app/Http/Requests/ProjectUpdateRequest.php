<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Project $project */
        $project = $this->route('project');

        return [
            'name' => [
                'min:1',
                Rule::unique('projects', 'name')
                    ->where('team_id', $project->getCachedTeam()->id)
                    ->ignore($project->id),
            ],
        ];
    }
}
