<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LanguageStoreRequest extends FormRequest
{
    use HasPreparation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        Gate::authorize('view', $this->route('team'));

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Team $team */
        $team = $this->route('team');

        return [
            'name' => [
                'required',
                Rule::unique('languages', 'name')
                    ->whereIn(
                        'id',
                        $team->languages->pluck('id')->toArray()
                    ),
            ],
            'form_ids' => [
                'array',
                Rule::exists('forms', 'id')
                    ->whereIn(
                        'id',
                        $team->forms->pluck('id')->toArray()
                    ),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->explode('form_ids');
    }
}
