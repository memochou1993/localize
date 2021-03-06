<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 * @property int $range_min
 * @property int $range_max
 * @property Collection $teams
 */
class Form extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'range_min',
        'range_max',
    ];

    /**
     * Get all of the teams that are assigned this form.
     *
     * @return MorphToMany
     */
    public function teams(): MorphToMany
    {
        return $this->morphedByMany(Team::class, 'model', 'model_has_forms');
    }

    /**
     * Get all of the values that are assigned this form.
     *
     * @return MorphToMany
     */
    public function values(): MorphToMany
    {
        return $this->morphedByMany(Value::class, 'model', 'model_has_forms');
    }

    /**
     * @return Team
     */
    public function getCachedTeam(): Team
    {
        $tag = sprintf('%s:%d', $this->getTable(), $this->getKey());

        return Cache::tags($tag)->sear('team', fn() => $this->teams->first());
    }
}
