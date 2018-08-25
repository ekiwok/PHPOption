<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

use function Ekiwok\Function1\optionWrap;

trait ScalarOptional
{
    protected $value;

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function map(callable $mapper): Option
    {
        return optionWrap($mapper($this->value));
    }

    public function ifPresent(callable $conumser)
    {
        if (!$this->isPresent()) {
            return;
        }

        $conumser($this->value);
    }
}
