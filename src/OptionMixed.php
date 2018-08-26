<?php
declare(strict_types=1);

namespace Ekiwok\Option;

use function Ekiwok\Function1\optionWrap;

abstract class OptionMixed implements Option
{
    use ScalarOptional;

    abstract public function orElse($value);

    static public function of($value): OptionMixed
    {
        if ($value === null) {
            return new class() extends OptionMixed implements None {

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
                    return $another instanceof OptionMixed
                        && $another instanceof None;
                }
            };
        }

        return new class($value) extends OptionMixed implements Some {

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
                return $other instanceof OptionMixed
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
