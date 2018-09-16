<?php
declare(strict_types=1);

namespace Ekiwok\Option;

/**
 * Method orElse is not declared in the interface because inheriting classes
 * typehint the $value parameter, for example in OptionString:
 *
 *  orElse(string $value): string
 *
 * Having orElse($value) in the interface would make it impossible to typehint the argument.
 *
 * orElse($value) returns given $value if Option is None, otherwise returns Option $value as get()
 *
 * @method mixed orElse($value)
 */
interface Option
{
    /**
     * Checks for equality with another Option.
     *
     * Two Options are equal when:
     *
     * - Both are Some or None
     * - Both are the same type fe. Optional, OptionString, OptionArray
     * - If they're Some they must have the same value
     *
     * Please notice that when Optional checks for equal if it contain objects it just checks
     * if objects are equal $obj1 == $obj2 . It does not check if these are both the same instance
     * $obj1 === $obj2 .
     */
    public function equals(Option $another): bool;

    /**
     * Checks if value is present.
     *
     * Returns true for Some.
     * Returns false for None.
     *
     * @return bool
     */
    public function isPresent(): bool;

    /**
     * If Option is Some calls mapper with value as parameter. Returns Some of type depending on value returned by mapper.
     *
     * For example for:
     *
     *    $maybeArray = OptionString::of("foo")
     *        ->map(function (string $string): array { return []; });
     *
     * $maybeArray is instance of OptionArray.
     *
     * If Option is None mapper is not called and None is returned.
     * If $typeToWrap is not provided the None is instance of Optional.
     *
     * If you want to map to Optional instead of Option<T> return instance of Any:
     *
     * For example for:
     *
     *    $maybeAnything = OptionString::of("foo")
     *        ->map(function (string $string): Any { return new Any([]); };
     *
     * $maybeAnything is instance of Optional which value is empty array.
     *
     * @param callable $mapper     Takes only one parameter which is Options value.
     * @param string $typeToWrap   If provided it will attempt to return Option<$typeToWrap> instead of Option<type of value returned by $mapper>
     *
     * @return Option
     */
    public function map(callable $mapper, string $typeToWrap = null): Option;

    /**
     * If Option is Some returns option value.
     * If Option is None throws NoSuchElementException
     *
     * @throws NoSuchElementException
     */
    public function get();

    /**
     * If Option is Some: Options value is returned.
     * If Option is None: $supplier is called and its return value is returned.
     */
    public function orElseGet(callable $supplier);

    /**
     * If Option is Some: Options value is returned.
     * If Option is None: Exception returned by $supplier is being thrown.
     *
     * @throws \Exception
     */
    public function orElseThrow(callable $supplier);

    /**
     * Returns Some for given $value.
     * If $value is null throws \InvalidArgumentException
     *
     * @throws \InvalidArgumentException
     */
    static public function Some($value): Some;

    /**
     * Returns None.
     */
    static public function None(): None;
}
