<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

trait ScalarOptional
{
    protected $value;

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function map(callable $mapper): AnyOptional
    {
        return AnyOptional::of($mapper($this->value));
    }

    public function ifPresent(callable $conumser)
    {
        if (!$this->isPresent()) {
            return;
        }

        $conumser($this->value);
    }
}
