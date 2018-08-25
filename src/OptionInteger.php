<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

use function Ekiwok\Function1\optionWrap;

abstract class OptionInteger implements Option
{
    use ScalarOptional;

    abstract public function get(): int;

    abstract public function orElse(int $value): int;

    abstract public function orElseGet(callable $supplier): int;

    abstract public function orElseThrow(callable $supplier): int;

    static public function of(int $value = null): OptionInteger
    {
        if ($value === null) {
            return new class() extends OptionInteger implements None {

                public function equals(Option $another): bool
                {
                    return $another instanceof OptionInteger
                        && $another instanceof None;
                }

                public function isPresent(): bool
                {
                    return false;
                }

                /**
                 * @throws NoSuchElementException
                 */
                public function get(): int
                {
                    throw new NoSuchElementException();
                }

                public function orElse(int $value): int
                {
                    return $value;
                }

                public function orElseGet(callable $supplier): int
                {
                    return $supplier();
                }

                public function orElseThrow(callable $supplier): int
                {
                    throw $supplier();
                }

                public function map(callable $mapper, string $typeToMap = null): Option
                {
                    return optionWrap(null, OptionString::of($typeToMap));
                }
            };
        }

        return new class($value) extends OptionInteger implements Some {

            public function __construct(int $value)
            {
                $this->value = $value;
            }

            public function equals(Option $another): bool
            {
                return $another instanceof OptionInteger
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
            public function get(): int
            {
                return $this->value;
            }

            public function orElse(int $value): int
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier): int
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier): int
            {
                return $this->value;
            }
        };
    }
}
