<?php
declare(strict_types=1);

namespace Ekiwok\Option;

/**
 * @method Any orElse($value)
 */
interface Option
{
    public function equals(Option $another): bool;

    public function isPresent(): bool;

    public function map(callable $mapper, string $typeToWrap = null): Option;

    public function get();

    public function orElseGet(callable $supplier);

    public function orElseThrow(callable $supplier);

    static public function Some($value): Some;

    static public function None(): None;
}
