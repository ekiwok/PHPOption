<?php

namespace Ekiwok\Option\Test;

use Ekiwok\Option\Any;
use Ekiwok\Option\Option;
use Ekiwok\Option\Optional;
use Ekiwok\Option\None;
use Ekiwok\Option\NoSuchElementException;
use Ekiwok\Option\OptionArray;
use Ekiwok\Option\OptionBoolean;
use Ekiwok\Option\OptionDouble;
use Ekiwok\Option\OptionInteger;
use Ekiwok\Option\OptionString;
use Ekiwok\Option\Some;
use PHPUnit\Framework\TestCase;

class Stub
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

/**
 * @method Any orElse($value)
 */
class OptionalStub implements Option
{
    static public function of($value)
    {
        return new class() extends OptionalStub implements Some {};
    }

    public function equals(Option $another): bool
    {
    }

    public function isPresent(): bool
    {
    }

    public function map(callable $mapper, string $typeToWrap = null): Option
    {
    }

    public function get()
    {
    }

    public function orElseGet(callable $supplier)
    {
    }

    public function orElseThrow(callable $supplier)
    {
    }

    static public function Some($value): Some
    {
    }

    static public function None(): None
    {
    }

    public function __call($name, $arguments)
    {
    }
}

class OptionalTest extends TestCase
{
    /**
     * @var Optional
     */
    private $some;

    /**
     * @var Optional
     */
    private $none;

    public function setUp()
    {
        $this->some = Optional::of(new Stub('test'));
        $this->none = Optional::of(null);
    }

    public function testOf()
    {
        $this->assertInstanceOf(Optional::class, $this->some);
        $this->assertInstanceOf(Some::class, $this->some);

        $this->assertInstanceOf(Optional::class, $this->none);
        $this->assertInstanceOf(None::class, $this->none);
    }

    public function testGet()
    {
        $this->assertEquals(new Stub("test"), $this->some->get());

        $this->expectException(NoSuchElementException::class);
        $this->none->get();
    }

    public function testOrElse()
    {
        $this->assertEquals(new Stub("test"), $this->some->orElse("something different"));
        $this->assertEquals("something different", $this->none->orElse("something different"));
    }

    public function testOrElseGet()
    {
        $supplier = function () {
            return 'supplied';
        };

        $this->assertEquals(new Stub("test"), $this->some->orElseGet($supplier));
        $this->assertEquals("supplied", $this->none->orElseGet($supplier));
    }

    public function testOrElseThrow()
    {
        $supplier = function () {
            return new \RuntimeException();
        };

        $this->assertEquals(new Stub("test"), $this->some->orElseThrow($supplier));

        $this->expectException(\RuntimeException::class);
        $this->none->orElseThrow($supplier);
    }

    public function testMap()
    {
        $mapper = function ($old) {
            return new Stub($old);
        };

        $this->assertEquals(Optional::of(new Stub(new Stub("test"))), $this->some->map($mapper));
        $this->assertEquals(Optional::of(null), $this->none->map($mapper));
    }

    public function testIsPresent()
    {
        $this->assertTrue($this->some->isPresent());
        $this->assertFalse($this->none->isPresent());
    }

    public function testIfPresent()
    {
        $flag = false;
        $caller = function () use (&$flag) {
            $flag = true;
        };

        $this->some->ifPresent($caller);
        $this->assertTrue($flag);

        $flag = false;
        $this->none->ifPresent($caller);
        $this->assertFalse($flag);
    }

    public function testEquals()
    {
        $this->assertTrue($this->some->equals(Optional::of(new Stub("test"))));
        $this->assertFalse($this->some->equals(Optional::of(new Stub("something different"))));
        $this->assertFalse($this->some->equals(Optional::of(null)));

        $this->assertTrue($this->none->equals(Optional::of(null)));
        $this->assertFalse($this->none->equals(Optional::of(new Stub("something different"))));
        $this->assertFalse($this->none->equals(Optional::of("something")));
    }

    public function testNone()
    {
        $this->assertInstanceOf(None::class, Optional::None());
        $this->assertInstanceOf(Optional::class, Optional::None());
    }

    public function testCustomMapping()
    {
        Optional::registerMappings([
            Stub::class => OptionalStub::class,
        ]);

        $this->assertInstanceOf(OptionalStub::class, Optional::Some(new Stub("test")));
    }

    /**
     * @dataProvider dataProviderSome
     */
    public function testSome($value, string $expectedClass)
    {
        $this->assertInstanceOf(Some::class, Optional::Some($value));
        $this->assertInstanceOf($expectedClass, Optional::Some($value));
    }

    public function dataProviderSome()
    {
        return [
            [new Any("test"), Optional::class],
            ["test", OptionString::class],
            [3.14, OptionDouble::class],
            [0, OptionInteger::class],
            [[], OptionArray::class],
            [false, OptionBoolean::class],
            [new \stdClass(), Optional::class],
        ];
    }
}
