<?php
declare(strict_types=1);

namespace Ekiwok\Function1;

use Ekiwok\Optional\Mixed;
use Ekiwok\Optional\Option;
use Ekiwok\Optional\OptionMixed;
use Ekiwok\Optional\OptionString;

function of(string $className, ...$args) {
    return $className::of(...$args);
};

function newish($className, ...$args) {
    return new $className(...$args);
}

function optionWrap($value): Option
{
    switch (gettype($value))
    {
        case "string":
            return OptionString::of($value);

        case "object":
            return OptionMixed::of($value instanceof Mixed ? $value->unwrap() : $value);

        case "boolean":
        case "integer":
        case "double":
        case "array":
        case "resource":
        case "resource (closed)":
        case "NULL":
        case "unknown type":
        default:
            return OptionMixed::of($value);
    }
}