<?php

namespace Bulldesk\Granatum;

class State extends ApiResource
{
    /**
     * Find a state by id.
     *
     * @param  string  $name
     * @return array|null
     */
    public function findById($id)
    {
        return $this->findBy('id', (int) $id);
    }

    /**
     * Find a state by name.
     *
     * @param  string  $name
     * @return array|null
     */
    public function findByName(string $name)
    {
        return $this->findBy('nome', $name);
    }
}
