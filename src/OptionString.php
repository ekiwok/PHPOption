<?php
declare(strict_types=1);

namespace Ekiwok\Option;

abstract class OptionString implements Option
{
    use ScalarOptional;

    /**
     * @throws NoSuchElementException
     * @return mixed
     */
    abstract public function get(): string;

    abstract public function orElse(string $value): string;

    abstract public function orElseGet(callable $supplier): string;

    abstract public function orElseThrow(callable $supplier): string;

    static public function of(string $value = null): OptionString
    {
        if ($value === null) {
            static::$none = static::$none ?: new class() extends OptionString implements None {

                public function equals(Option $another): bool
                {
                    return $another instanceof OptionString
                        && $another instanceof None;
                }

                public function isPresent(): bool
                {
                    return false;
                }

                /**
                 * @throws NoSuchElementException
                 */
                public function get(): string
                {
                    throw new NoSuchElementException();
                }

                public function orElse(string $value): string
                {
                    return $value;
                }

                public function orElseGet(callable $supplier): string
                {
                    return $supplier();
                }

                public function orElseThrow(callable $supplier): string
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

        return new class($value) extends OptionString implements Some {

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function equals(Option $another): bool
            {
                return $another instanceof OptionString
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
            public function get(): string
            {
                return $this->value;
            }

            public function orElse(string $value): string
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier): string
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier): string
            {
                return $this->value;
            }
        };
    }
}
