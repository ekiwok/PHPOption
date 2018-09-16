<?php
declare(strict_types=1);

namespace Ekiwok\Option;

use function Ekiwok\Function1\optionWrap;

abstract class OptionArray implements Option
{
    use ScalarOptional;

    abstract public function get(): array;

    abstract public function orElse(array $value): array;

    abstract public function orElseGet(callable $supplier): array;

    abstract public function orElseThrow(callable $supplier): array;

    static public function of(array $value = null): OptionArray
    {
        if ($value === null) {
            static::$none = static::$none ?: new class() extends OptionArray implements None {

                public function equals(Option $another): bool
                {
                    return $another instanceof OptionArray
                        && $another instanceof None;
                }

                public function isPresent(): bool
                {
                    return false;
                }

                /**
                 * @throws NoSuchElementException
                 */
                public function get(): array
                {
                    throw new NoSuchElementException();
                }

                public function orElse(array $value): array
                {
                    return $value;
                }

                public function orElseGet(callable $supplier): array
                {
                    return $supplier();
                }

                public function orElseThrow(callable $supplier): array
                {
                    throw $supplier();
                }

                public function map(callable $mapper, string $typeToMap = null): Option
                {
                    return Optional::optionWrap(null, OptionString::of($typeToMap));
                }
            };

            return static::$none;
        }

        return new class($value) extends OptionArray implements Some {

            public function __construct(array $value)
            {
                $this->value = $value;
            }

            public function equals(Option $another): bool
            {
                return $another instanceof OptionArray
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
            public function get(): array
            {
                return $this->value;
            }

            public function orElse(array $value): array
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier): array
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier): array
            {
                return $this->value;
            }
        };
    }
}
