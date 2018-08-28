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

/**
 * @internal
 */
function optionWrap($value, OptionString $typeToWrap): Option
{
    switch ($typeToWrap->orElse(gettype($value)))
    {
        case "string":
            return OptionString::of($value);

        case "object":
            return Optional::of($value instanceof Any ? $value->unwrap() : $value);

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
    }
}