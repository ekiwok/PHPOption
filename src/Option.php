<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

interface Option
{
    public function equals(Option $another): bool;

    public function isPresent(): bool;

    public function map(callable $mapper): Option;
}
