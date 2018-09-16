<?php
declare(strict_types=1);

namespace Ekiwok\Option\Test\Function1;

require_once __DIR__ . '/../vendor/autoload.php';

function of(string $className, ...$args) {
    return $className::of(...$args);
};
