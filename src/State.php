<?php

namespace Bulldesk\Granatum;

class State extends ApiResource
{
    /**
     * Find a state by id.
     */
    public function findById($id)
    {
        return $this->findBy('id', (int) $id);
    }

    /**
     * Find a state by name.
     */
    public function findByName(string $name)
    {
        return $this->findBy('nome', $name);
    }
}
