<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

interface Optional
{
    public function equals(Optional $another): bool;

    public function isPresent(): bool;
}
