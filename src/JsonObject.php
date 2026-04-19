<?php

declare(strict_types=1);

namespace losthost\JsonObject;

use BadMethodCallException;
use JsonException;
use stdClass;

final class JsonObject
{
    protected stdClass|array $decoded;

    public function __construct(string|stdClass $json = '{}')
    {
        if (is_string($json)) {
            $decoded = json_decode($json, false, 512, JSON_THROW_ON_ERROR);

            if (!$decoded instanceof stdClass && !is_array($decoded)) {
                throw new JsonException('Decoded JSON value must be an object or array.');
            }

            $this->decoded = $decoded;
            return;
        }

        $this->decoded = $json;
    }

    public function __call(string $method, array $arguments): mixed
    {
        if (str_starts_with($method, 'get')) {
            return $this->getFieldValue($this->getterNameToField($method));
        }

        if (str_starts_with($method, 'is')) {
            return (bool) ($this->getFieldValue($this->booleanMethodToField($method)) ?? false);
        }

        throw new BadMethodCallException(sprintf('Method %s is not supported.', $method));
    }

    public function has(string $field): bool
    {
        $decoded = $this->decode();

        if ($decoded instanceof stdClass) {
            return property_exists($decoded, $field);
        }

        return array_key_exists($field, $decoded);
    }

    public function toArray(): array|stdClass
    {
        return $this->decode();
    }

    protected function getterNameToField(string $method): string
    {
        if (!str_starts_with($method, 'get') || strlen($method) <= 3) {
            throw new BadMethodCallException(sprintf('Method %s is not a getter.', $method));
        }

        return $this->pascalCaseToSnakeCase(substr($method, 3));
    }

    protected function booleanMethodToField(string $method): string
    {
        if (!str_starts_with($method, 'is') || strlen($method) <= 2) {
            throw new BadMethodCallException(sprintf('Method %s is not a boolean getter.', $method));
        }

        return 'is_' . $this->pascalCaseToSnakeCase(substr($method, 2));
    }

    protected function pascalCaseToSnakeCase(string $value): string
    {
        $value = preg_replace('/(?<!^)[A-Z]/', '_$0', $value) ?? $value;

        return strtolower($value);
    }

    protected function getFieldValue(string $field): mixed
    {
        $data = $this->decode();

        if ($data instanceof stdClass) {
            if (!property_exists($data, $field)) {
                return null;
            }

            return $this->wrap($data->{$field});
        }

        if (!array_key_exists($field, $data)) {
            return null;
        }

        return $this->wrap($data[$field]);
    }

    protected function decode(): stdClass|array
    {
        return $this->decoded;
    }

    protected function wrap(mixed $value): mixed
    {
        if ($value instanceof stdClass) {
            return new self($value);
        }

        if (!is_array($value)) {
            return $value;
        }

        if ($this->isList($value)) {
            return array_map(
                fn (mixed $item): mixed => $item instanceof stdClass ? new self($item) : $item,
                $value
            );
        }

        throw new JsonException('Associative arrays are not supported as wrapped values.');
    }

    /**
     * @param array<mixed> $value
     */
    protected function isList(array $value): bool
    {
        return array_is_list($value);
    }
}
