<?php
declare(strict_types=1);

namespace Ekiwok\Option;

abstract class OptionInteger implements Option
{
    use ScalarOptional;

    /**
     * {@inheritdoc}
     */
    abstract public function get(): int;

    /**
     * {@inheritdoc}
     */
    abstract public function orElse(int $value): int;

    /**
     * {@inheritdoc}
     */
    abstract public function orElseGet(callable $supplier): int;

    /**
     * {@inheritdoc}
     */
    abstract public function orElseThrow(callable $supplier): int;

    /**
     * If $value is not null returns Some OptionInteger, otherwise returns None OptionInteger.
     */
    static public function of(int $value = null): OptionInteger
    {
        if ($value === null) {
            static::$none = static::$none ?: new class() extends OptionInteger implements None {

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
                    return Optional::optionWrap(null, OptionString::of($typeToMap));
                }
            };

            return static::$none;
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
