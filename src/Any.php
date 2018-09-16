<?php
declare(strict_types=1);

namespace Ekiwok\Option;

/**
 * This class is used to wrap values to indicate that Option should not be strict.
 *
 * For example:
 *
 * $optionAny = Optional::Some(new Any("Any allows to map option to anything."));
 * $stringOrArray = $optionAny->orElseGet([]);
 * // ok
 *
 * without wrapping in Any:
 *
 * $optionString = Optional::Some("Any allows to map option to anything.");
 * $stringOrArray = $optionString->orElseGet([]);
 * // TypeError: Argument 1 passed to class@anonymous::orElse() must be of the type string, array given, called in ...
 *
 *
 * It might be also used in map:
 *
 * $errorOrProduct = $maybeId->map(function (string $id): Any {
 *    return new Any($this->products->findOneById($id));
 * })
 * ->orElse(Error::productNotFound());
 */
final class Any
{
    /**
     * @var mixed
     */
    private $mixed;

    public function __construct($mixed)
    {
        $this->mixed = $mixed;
    }

    /**
     * @return mixed
     */
    public function unwrap()
    {
        return $this->mixed;
    }
}
