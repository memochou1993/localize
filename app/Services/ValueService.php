<?php

namespace App\Services;

use App\Models\Key;
use App\Models\Value;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ValueService
{
    /**
     * @var Value
     */
    private Value $value;

    /**
     * Instantiate a new service instance.
     *
     * @param  Value  $value
     */
    public function __construct(
        Value $value
    ) {
        $this->value = $value;
    }

    /**
     * @param  Value  $value
     * @param  Request  $request
     * @return Model
     */
    public function get(Value $value, Request $request): Model
    {
        return $this->value
            ->with($request->relations ?? [])
            ->find($value->id);
    }

    /**
     * @param  Key  $key
     * @param  Request  $request
     * @return Model
     */
    public function store(Key $key, Request $request): Model
    {
        $value = $key->values()->create($request->all());

        $value->languages()->attach($request->language_id);
        $value->forms()->attach($request->form_id);

        return $value;
    }

    /**
     * @param  Value  $value
     * @param  Request  $request
     * @return Model
     */
    public function update(Value $value, Request $request): Model
    {
        $value->update($request->all());

        return $value;
    }

    /**
     * @param  Value  $value
     * @return bool
     */
    public function destroy(Value $value): bool
    {
        return $this->value->destroy($value->id);
    }
}
