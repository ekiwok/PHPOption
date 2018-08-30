<?php
$fullClassName = $argv[1];
$lastSeparatorPos = strrpos($fullClassName, '\\', -1);
$shortClassName = $lastSeparatorPos === false
    ? sprintf('\\%s', $fullClassName)
    : substr($fullClassName, $lastSeparatorPos+1);
$namespace = $argv[2] ?? sprintf('autogenerated\\%s', substr($fullClassName, 0, $lastSeparatorPos));

echo "<?php" . PHP_EOL;
?>
declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use function Ekiwok\Function1\optionWrap;

use Ekiwok\Option\None;
use Ekiwok\Option\Option;
use Ekiwok\Option\OptionString;
use Ekiwok\Option\ScalarOptional;
use Ekiwok\Option\Some;

use <?php echo $fullClassName; ?>;

abstract class Option<?php echo $shortClassName; ?> implements Option
{
    use ScalarOptional;

    /**
     * @throws NoSuchElementException
     * @return mixed
     */
    abstract public function get(): <?php echo $shortClassName; ?>;

    abstract public function orElse(<?php echo $shortClassName; ?> $value): <?php echo $shortClassName; ?>;

    abstract public function orElseGet(callable $supplier): <?php echo $shortClassName; ?>;

    abstract public function orElseThrow(callable $supplier): <?php echo $shortClassName; ?>;

    static public function of(string $value = null): Option<?php echo $shortClassName; ?>
    {
        if ($value === null) {
            return new class() extends Option<?php echo $shortClassName; ?> implements None {

                public function equals(Option $another): bool
                {
                    return $another instanceof Option<?php echo $shortClassName . PHP_EOL; ?>
                        && $another instanceof None;
                }

                public function isPresent(): bool
                {
                    return false;
                }

                /**
                 * @throws NoSuchElementException
                 */
                public function get(): <?php echo $shortClassName; ?>
                {
                    throw new NoSuchElementException();
                }

                public function orElse(<?php echo $shortClassName; ?> $value): <?php echo $shortClassName . PHP_EOL; ?>
                {
                    return $value;
                }

                public function orElseGet(callable $supplier): <?php echo $shortClassName . PHP_EOL; ?>
                {
                    return $supplier();
                }

                public function orElseThrow(callable $supplier): <?php echo $shortClassName . PHP_EOL; ?>
                {
                    throw $supplier();
                }

                public function map(callable $mapper, string $typeToMap = null): Option
                {
                    return optionWrap(null, OptionString::of($typeToMap));
                }
            };
        }

        return new class($value) extends Option<?php echo $shortClassName; ?> implements Some {

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function equals(Option $another): bool
            {
                return $another instanceof Option<?php echo $shortClassName . PHP_EOL; ?>
                    && $another instanceof Some
                    && $another->get() == $this->value;
            }

            public function isPresent(): bool
            {
                return true;
            }

            /**
             * @throws NoSuchElementException
             */
            public function get(): <?php echo $shortClassName . PHP_EOL; ?>
            {
                return $this->value;
            }

            public function orElse(<?php echo $shortClassName; ?> $value): <?php echo $shortClassName . PHP_EOL; ?>
            {
                return $this->value;
            }

            public function orElseGet(callable $supplier): <?php echo $shortClassName . PHP_EOL; ?>
            {
                return $this->value;
            }

            public function orElseThrow(callable $supplier): <?php echo $shortClassName . PHP_EOL; ?>
            {
                return $this->value;
            }
        };
    }
}