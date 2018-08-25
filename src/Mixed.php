<?php
declare(strict_types=1);

namespace Ekiwok\Optional;

final class Mixed
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
