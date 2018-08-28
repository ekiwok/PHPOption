<?php
declare(strict_types=1);

namespace Ekiwok\Option;

use function Ekiwok\Function1\optionWrap;

abstract class Optional implements Option
{
    use ScalarOptional;

    abstract public function orElse($value);

    static public function Some($value): Some
    {
        if ($value === null) {
            throw new \InvalidArgumentException(ERROR_MSG_SOME_FROM_NONE);
        }
        return optionWrap($value, OptionString::None());
    }

    static public function None(): None
    {
        return self::of(null);
    }


    static public function of($value): Optional
    {
        if ($value === null) {
            return new class() extends Optional implements None {

                public function get()
                {
                    throw new NoSuchElementException();
                }

                public function orElseThrow(callable $supplier)
                {
                    throw $supplier(null);
                }

                public function map(callable $callback, string $typeToWrap = null): Option
                {
                    return optionWrap(null, OptionString::of($typeToWrap));
                }

                public function isPresent(): bool
                {
                    return false;
                }

                public function orElse($value)
                {
                    return $value;
                }

                public function orElseGet(callable $supplier)
                {
                    return $supplier();
                }

                public function equals(Option $another): bool
                {
                    return $another instanceof Optional
                        && $another instanceof None;
                }
            };
        }

        return new class($value) extends Optional implements Some {

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function get()
            {
                return $this->value;
            }

            public function orElse($value)
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier)
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier)
            {
                return $this->value;
            }

            public function equals(Option $other): bool
            {
                return $other instanceof Optional
                    && $other instanceof Some
                    && $this->get() == $other->get();
            }

            public function isPresent(): bool
            {
                return true;
            }
        };
    }
}
