<?php
declare(strict_types=1);

namespace Ekiwok\Option;

abstract class OptionDouble implements Option
{
    use ScalarOptional;

    abstract public function get(): float;

    abstract public function orElse(float $value): float;

    abstract public function orElseGet(callable $supplier): float;

    abstract public function orElseThrow(callable $supplier): float;

    static public function of(float $value = null): OptionDouble
    {
        if ($value === null) {
            self::$none = self::$none ?: new class() extends OptionDouble implements None {

                public function equals(Option $another): bool
                {
                    return $another instanceof OptionDouble
                        && $another instanceof None;
                }

                public function isPresent(): bool
                {
                    return false;
                }

                /**
                 * @throws NoSuchElementException
                 */
                public function get(): float
                {
                    throw new NoSuchElementException();
                }

                public function orElse(float $value): float
                {
                    return $value;
                }

                public function orElseGet(callable $supplier): float
                {
                    return $supplier();
                }

                public function orElseThrow(callable $supplier): float
                {
                    throw $supplier();
                }

                public function map(callable $mapper, string $typeToMap = null): Option
                {
                    return Optional::optionWrap(null, OptionString::of($typeToMap));
                }
            };

            return self::$none;
        }

        return new class($value) extends OptionDouble implements Some {

            public function __construct(float $value)
            {
                $this->value = $value;
            }

            public function equals(Option $another): bool
            {
                return $another instanceof OptionDouble
                    && $another instanceof Some
                    && $another->get() == $this->value;
            }

            public function isPresent(): bool
            {
                return true;
            }

            /**
             * @throws NoSuchElementException
             * @return mixed
             */
            public function get(): float
            {
                return $this->value;
            }

            public function orElse(float $value): float
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier): float
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier): float
            {
                return $this->value;
            }
        };
    }
}
