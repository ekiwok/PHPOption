<?php
declare(strict_types=1);

namespace Ekiwok\Option;

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

    public function map(callable $mapper, string $typeToWrap = null): Option
    {
        return optionWrap($mapper($this->value), OptionString::of($typeToWrap));
    }

    public function ifPresent(callable $conumser)
    {
        if (!$this->isPresent()) {
            return;
        }

        $conumser($this->value);
    }
}
