<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\HasPreparation;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageUpdateRequest extends FormRequest
{
    use HasPreparation;

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
        /** @var Language $language */
        $language = $this->route('language');

        return [
            'name' => [
                'min:1',
                Rule::unique('languages', 'name')
                    ->whereIn(
                        'id',
                        $language->getCachedTeam()->languages->pluck('id')->toArray()
                    )
                    ->ignore($language->id),
            ],
            'form_ids' => [
                'array',
                Rule::exists('forms', 'id')
                    ->whereIn(
                        'id',
                        $language->getCachedTeam()->forms->pluck('id')->toArray()
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
