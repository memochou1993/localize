<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LanguageService
{
    /**
     * @var Language
     */
    private Language $language;

    /**
     * Instantiate a new service instance.
     *
     * @param  Language  $language
     */
    public function __construct(
        Language $language
    ) {
        $this->language = $language;
    }

    /**
     * @param  Language  $language
     * @param  Request  $request
     * @return Model
     */
    public function get(Language $language, Request $request): Model
    {
        return $this->language
            ->with($request->relations ?? [])
            ->find($language->id);
    }

    /**
     * @param  Language  $language
     * @param  array  $data
     * @param  array|null  $form_ids
     * @return Model
     */
    public function update(Language $language, array $data, array $form_ids = []): Model
    {
        $language = $this->language->find($language->id);

        $language->update($data);

        if ($form_ids) {
            $language->forms()->sync($form_ids);
        }

        return $language;
    }

    /**
     * @param  Language  $language
     * @return bool
     */
    public function destroy(Language $language): bool
    {
        return $this->language->destroy($language->id);
    }

    /**
     * @param  Language  $language
     * @param  array  $form_ids
     * @param  bool  $detaching
     */
    public function attachForm(Language $language, array $form_ids, bool $detaching): void
    {
        $language->forms()->sync($form_ids, $detaching);
    }

    /**
     * @param  Language  $language
     * @param  int  $form_id
     */
    public function detachForm(Language $language, int $form_id): void
    {
        $language->forms()->detach($form_id);
    }
}
