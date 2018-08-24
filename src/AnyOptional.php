<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

abstract class AnyOptional implements Optional
{
    use ScalarOptional;

    abstract public function get();

    abstract public function orElse($value);

    abstract public function orElseGet(callable $supplier);

    abstract public function orElseThrow(callable $supplier);

    static public function of($value)
    {
        if ($value === null) {
            return new class() extends AnyOptional implements None {

                public function get()
                {
                    throw new NoSuchElementException();
                }

                public function orElseThrow(callable $supplier)
                {
                    throw $supplier(null);
                }

                public function map(callable $callback): AnyOptional
                {
                    return AnyOptional::of(null);
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

                public function equals(Optional $another): bool
                {
                    return $another instanceof AnyOptional
                        && $another instanceof None;
                }
            };
        }

        return new class($value) extends AnyOptional implements Some {

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

            public function equals(Optional $other): bool
            {
                return $other instanceof AnyOptional
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
