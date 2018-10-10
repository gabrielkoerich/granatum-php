<?php

namespace Bulldesk\Granatum;

class State extends ApiResource
{
    /**
     * Find a state by id.
     */
    public function findById($id): ?self
    {
        return $this->findBy('id', (int) $id);
    }

    /**
     * Find a state by name.
     */
    public function findByName(string $name): ?self
    {
        return $this->findBy('nome', $name);
    }
}
