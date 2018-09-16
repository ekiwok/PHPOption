<?php
declare(strict_types=1);

namespace Ekiwok\Option;

abstract class Optional implements Option
{
    use ScalarOptional;

    const ERROR_MSG_SOME_FROM_NONE = 'Cannot make Some from None';

    /**
     * {@inheritdoc}
     */
    abstract public function orElse($value);

    /**
     * Used to store mappings for dynamically registered options.
     * @var array
     */
    private static $mappings = [];

    /**
     * {@inheritdoc}
     *
     * Please notice that in contrary to specific Option<T> classes Optional tries
     * to return the most specific Option<T> it can. So for example for:
     *
     *    $maybeString = Optional::Some('foo');
     *
     * $maybeString is instance of OptionString.
     *
     * To get Optional provide Any:
     *
     *    $maybeAnything = Optional::some(new Any('foo'));
     *
     * $maybeAnything is instance of Optional.
     */
    static public function Some($value): Some
    {
        if ($value === null) {
            throw new \InvalidArgumentException(static::ERROR_MSG_SOME_FROM_NONE);
        }
        return Optional::optionWrap($value, OptionString::None());
    }

    /**
     * Registers array of mappings in format:
     *  [
     *     className => Option<className>
     *  ]
     */
    static public function registerMappings(array $mappings)
    {
        self::$mappings = array_merge(self::$mappings, $mappings);
    }

    /**
     * Maybe returns a name of the Option class registered for $className.
     */
    static public function getMapping(string $className): OptionString
    {
        return OptionString::of(self::$mappings[$className] ?? null);
    }

    /**
     * {@inheritdoc}
     */
    static public function None(): None
    {
        return self::of(null);
    }

    /**
     * If $value is not null returns Some Optional, otherwise returns None Optional.
     */
    static public function of($value): Optional
    {
        if ($value === null) {
            self::$none = self::$none ?: new class() extends Optional implements None {

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
                    return self::optionWrap(null, OptionString::of($typeToWrap));
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

            return self::$none;
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

    /**
     * Wraps given $value into a corresponding Option<T> class.
     * All objects which don't have registered custom Option classes are wrapped into an Optional.
     * The only exception is Any which is unwrapped and than wrapped into an Optional.
     * @see Any
     *
     * Please notice that this is for internal usage only.
     *
     * @internal
     */
    final static public function optionWrap($value, OptionString $typeToWrap): Option
    {
        switch ($typeToWrap->orElse(gettype($value)))
        {
            case "string":
                return OptionString::of($value);

            case "boolean":
                return OptionBoolean::of($value);

            case "integer":
                return OptionInteger::of($value);

            case "double":
                return OptionDouble::of($value);

            case "array":
                return OptionArray::of($value);

            case "resource":
            case "resource (closed)":
            case "NULL":
            case "unknown type":
            default:
                return Optional::of($value);

            case "object":
                return Optional::getMapping(get_class($value))
                    ->map(function (string $optionClassName) use ($value) {
                        return $optionClassName::of($value);
                    })
                    ->orElseGet(function () use ($value) {
                        return Optional::of($value instanceof Any ? $value->unwrap() : $value);
                    });

        }
    }
}
