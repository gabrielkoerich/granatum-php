<?php

namespace Bulldesk\Granatum;

use ArrayAccess;
use Psr\Http\Message\ResponseInterface;
use Tightenco\Collect\Support\Collection;

abstract class GranatumObject implements ArrayAccess
{
    /**
     * The object values.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Create a new instance.
     *
     * @return self
     */
    abstract protected function newInstance();

    /**
     * Transform a response into a collection.
     */
    protected function toCollection(ResponseInterface $response): Collection
    {
        $items = array_map(function ($item) {
            return $this->newInstance()->setValues($item);
        }, (array) $this->getContentFromResponse($response));

        return new Collection($items);
    }

    /**
     * Create the object based on a response.
     */
    protected function createFromResponse(ResponseInterface $response): self
    {
        return $this->newInstance()->setValues($this->getContentFromResponse($response));
    }

    /**
     * Get the response content.
     */
    protected function getContentFromResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Set the object values.
     */
    public function setValues($values)
    {
        $this->values = (array) $values;

        return $this;
    }

    /**
     * Set the value for a given offset.
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    /**
     * Determine if the given attribute.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }

    /**
     * Unset the value for a given offset.
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * Get the value for a given offset.
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset] ?? null;
    }

    /**
     * Dynamically retrieve a value.
     */
    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Return as an array.
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * Return as a string.
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}
