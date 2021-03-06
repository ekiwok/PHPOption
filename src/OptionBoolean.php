<?php
declare(strict_types=1);

namespace Ekiwok\Option;

abstract class OptionBoolean implements Option
{
    use ScalarOptional;

    /**
     * {@inheritdoc}
     */
    abstract public function get(): bool;

    /**
     * {@inheritdoc}
     */
    abstract public function orElse(bool $value): bool;

    /**
     * {@inheritdoc}
     */
    abstract public function orElseGet(callable $supplier): bool;

    /**
     * {@inheritdoc}
     */
    abstract public function orElseThrow(callable $supplier): bool;

    /**
     * If $value is not null returns Some OptionBoolean, otherwise returns None OptionBoolean.
     */
    static public function of(bool $value = null): OptionBoolean
    {
        if ($value === null) {
            static::$none = static::$none ?: new class() extends OptionBoolean implements None {

                public function equals(Option $another): bool
                {
                    return $another instanceof OptionBoolean
                        && $another instanceof None;
                }

                public function isPresent(): bool
                {
                    return false;
                }

                /**
                 * @throws NoSuchElementException
                 */
                public function get(): bool
                {
                    throw new NoSuchElementException();
                }

                public function orElse(bool $value): bool
                {
                    return $value;
                }

                public function orElseGet(callable $supplier): bool
                {
                    return $supplier();
                }

                public function orElseThrow(callable $supplier): bool
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

        return new class($value) extends OptionBoolean implements Some {

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function equals(Option $another): bool
            {
                return $another instanceof OptionBoolean
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
            public function get(): bool
            {
                return $this->value;
            }

            public function orElse(bool $value): bool
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier): bool
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier): bool
            {
                return $this->value;
            }
        };
    }
}
