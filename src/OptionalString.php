<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

class OptionalString
{
    use ScalarOptional;

    private function __construct(string $maybeString = null)
    {
        $this->value = $maybeString;
    }

    static public function of(string $maybeString = null)
    {
        return new self($maybeString);
    }

    public function get(): string
    {
        if ($this->value === null) {
            throw new NoSuchElementException();
        }

        return $this->value;
    }

    public function orElse($other)
    {

    }
}
