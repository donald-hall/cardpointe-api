<?php

namespace Dewbud\CardConnect;

use ArrayAccess;
use JsonSerializable;
use ReturnTypeWillChange;

class Fluent implements ArrayAccess, JsonSerializable
{
    /**
     * All of the attributes set on the container.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Attribute casting.
     */
    protected $casts = [];

    /**
     * Create a new fluent container instance.
     *
     * @param array|object $attributes
     */
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get an attribute from the container.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null): mixed {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * Get the attributes from the container.
     *
     * @return array
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

	/**
	 * Cast an attribute.
	 *
	 * @param string $type
	 * @param string $key
	 *
	 * @return mixed|string
	 */
	#[ReturnTypeWillChange] protected function castAttribute(string $type, string $key): mixed {
        $thing = $this->get($key);

        switch ($type) {
            case 'bool':
                if (is_bool($thing)) {
                    return true === $thing ? 'Y' : 'N';
                }
                // Allows 'Y' and 'N' too.
                return $thing;
                break;
            default:
                return $thing;
                break;
        }
    }

    /**
     * Convert the Fluent instance to an array.
     *
     * @return array
     */
	#[ReturnTypeWillChange] public function toArray(): array {
        $data = $this->attributes;

        foreach ($this->casts as $key => $type) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $this->castAttribute($type, $key);
            }
        }

        return $data;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->toArray();
    }

    /**
     * Convert the Fluent instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    #[ReturnTypeWillChange] public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed  $value
     */
    #[ReturnTypeWillChange] public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param string $offset
     */
    #[ReturnTypeWillChange] public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return $this
     */
    public function __call(string $method, array $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set(string $key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset(string $key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param string $key
     */
    public function __unset(string $key)
    {
        $this->offsetUnset($key);
    }
}