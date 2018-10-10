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
     *
     * @param  ResponseInterface $response
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
     *
     * @param  ResponseInterface $response
     * @return self
     */
    protected function createFromResponse(ResponseInterface $response): self
    {
        return $this->newInstance()->setValues($this->getContentFromResponse($response));
    }

    /**
     * Get the response content.
     *
     * @param  ResponseInterface $response
     * @return mixed
     */
    protected function getContentFromResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Set the object values.
     *
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = (array) $values;

        return $this;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
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
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset] ?? null;
    }

    /**
     * Dynamically retrieve a value.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Return as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * Return as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}
