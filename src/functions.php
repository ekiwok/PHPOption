<?php
declare(strict_types=1);

namespace Ekiwok\Function1;

use Ekiwok\Optional\Mixed;
use Ekiwok\Optional\Option;
use Ekiwok\Optional\OptionArray;
use Ekiwok\Optional\OptionBoolean;
use Ekiwok\Optional\OptionDouble;
use Ekiwok\Optional\OptionInteger;
use Ekiwok\Optional\OptionMixed;
use Ekiwok\Optional\OptionString;

function of(string $className, ...$args) {
    return $className::of(...$args);
};

function newish($className, ...$args) {
    return new $className(...$args);
}

function optionWrap($value, OptionString $typeToWrap): Option
{
    switch ($typeToWrap->orElse(gettype($value)))
    {
        case "string":
            return OptionString::of($value);

        case "object":
            return OptionMixed::of($value instanceof Mixed ? $value->unwrap() : $value);

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
            return OptionMixed::of($value);
    }
}