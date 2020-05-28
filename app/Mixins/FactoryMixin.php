<?php

namespace App\Mixins;

use Closure;
use Illuminate\Database\Eloquent\Model;

class FactoryMixin
{
    /**
     * @var Model
     */
    private Model $class;

    /**
     * @return Closure
     */
    public function withoutEvents()
    {
        return function () {
            $this->class::flushEventListeners();

            return $this;
        };
    }
}