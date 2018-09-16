<?php
declare(strict_types=1);

namespace Ekiwok\Function1;

use Ekiwok\Option\Any;
use Ekiwok\Option\Option;
use Ekiwok\Option\OptionArray;
use Ekiwok\Option\OptionBoolean;
use Ekiwok\Option\OptionDouble;
use Ekiwok\Option\OptionInteger;
use Ekiwok\Option\Optional;
use Ekiwok\Option\OptionString;

define('ERROR_MSG_SOME_FROM_NONE', 'Cannot make Some from None');

/**
 * @internal
 */
function of(string $className, ...$args) {
    return $className::of(...$args);
};
