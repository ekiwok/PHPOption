<?php
declare(strict_types=1);

namespace Ekiwok\Option;

/**
 * This exception is thrown where there is an attempt to get value from None.
 */
class NoSuchElementException extends \LogicException
{
}
