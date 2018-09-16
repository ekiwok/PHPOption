<?php
declare(strict_types=1);

namespace Ekiwok\Option;

trait ScalarOptional
{
    protected $value;

    /**
     * @var $this
     */
    static private $none;

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function map(callable $mapper, string $typeToWrap = null): Option
    {
        return Optional::optionWrap($mapper($this->value), OptionString::of($typeToWrap));
    }

    public function ifPresent(callable $conumser)
    {
        if (!$this->isPresent()) {
            return;
        }

        $conumser($this->value);
    }

    static public function Some($value): Some
    {
        if ($value === null) {
            throw new \InvalidArgumentException(Optional::ERROR_MSG_SOME_FROM_NONE);
        }
        return self::of($value);
    }

    static public function None(): None
    {
        return self::of(null);
    }
}
