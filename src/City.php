<?php

namespace Bulldesk\Granatum;

use Tightenco\Collect\Support\Collection;

class City extends ApiResource
{
    /**
     * Find a city from a given state.
     */
    public function getFromState($state_id): Collection
    {
        $options['query']['estado_id'] = $state_id;

        return parent::all($options);
    }
}
