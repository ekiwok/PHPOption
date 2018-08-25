<?php
declare(strict_types=1);

namespace Ekiwok\Optional\Test;

use function Ekiwok\Function1\of;
use Ekiwok\Optional\Mixed;
use Ekiwok\Optional\None;
use Ekiwok\Optional\NoSuchElementException;
use Ekiwok\Optional\OptionMixed;
use Ekiwok\Optional\OptionString;
use Ekiwok\Optional\Some;
use PHPUnit\Framework\TestCase;

abstract class ScalarOptionTest extends TestCase
{
    const TESTED_CLASS = null;
    const SOME_VALUE = null;
    const OR_ELSE_VALUE = null;

    /**
     * @var mixed
     */
    protected $some;

    /**
     * @var mixed
     */
    protected $none;

    public function setUp()
    {
        $this->some = of(static::TESTED_CLASS, static::SOME_VALUE);
        $this->none = of(static::TESTED_CLASS, null);
    }

    public function testOf()
    {
        $this->assertInstanceOf(static::TESTED_CLASS, $this->some);
        $this->assertInstanceOf(Some::class, $this->some);

        $this->assertInstanceOf(static::TESTED_CLASS, $this->none);
        $this->assertInstanceOf(None::class, $this->none);
    }

    public function testGet()
    {
        $this->assertEquals(static::SOME_VALUE, $this->some->get());

        $this->expectException(NoSuchElementException::class);
        $this->none->get();
    }

    public function testOrElse()
    {
        $this->assertEquals(static::SOME_VALUE, $this->some->orElse(static::OR_ELSE_VALUE));
        $this->assertEquals(static::OR_ELSE_VALUE, $this->none->orElse(static::OR_ELSE_VALUE));
    }

    public function testOrElseGet()
    {
        $supplier = function () {
            return static::OR_ELSE_VALUE;
        };

        $this->assertEquals(static::SOME_VALUE, $this->some->orElseGet($supplier));
        $this->assertEquals(static::OR_ELSE_VALUE, $this->none->orElseGet($supplier));
    }

    public function testOrElseThrow()
    {
        $supplier = function () {
            return new \RuntimeException();
        };

        $this->assertEquals(static::SOME_VALUE, $this->some->orElseThrow($supplier));

        $this->expectException(\RuntimeException::class);
        $this->none->orElseThrow($supplier);
    }

    /**
     * @dataProvider dataProviderTestMap
     */
    public function testMap(string $expectedOptionClass, $expectedOptionValue, callable $mapper)
    {
        $this->assertInstanceOf($expectedOptionClass, $this->some->map($mapper));
        $this->assertEquals($expectedOptionValue, $this->some->map($mapper)->get());
        $this->assertTrue($this->none->map($mapper)->equals(OptionMixed::of(null)));
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
        $this->assertTrue($this->some->equals(of(static::TESTED_CLASS, static::SOME_VALUE)));
        $this->assertFalse($this->some->equals(of(static::TESTED_CLASS, static::OR_ELSE_VALUE)));
        $this->assertFalse($this->some->equals(of(static::TESTED_CLASS, null)));

        $this->assertTrue($this->none->equals(of(static::TESTED_CLASS, null)));
        $this->assertFalse($this->none->equals(of(static::TESTED_CLASS, static::SOME_VALUE)));
        $this->assertFalse($this->none->equals(of(static::TESTED_CLASS, static::OR_ELSE_VALUE)));
    }

    public function dataProviderTestMap()
    {
        return [
            'identity' => [static::TESTED_CLASS, static::SOME_VALUE,  function ($value) { return static::SOME_VALUE; }],
            'string'   => [OptionString::class, "test string", function ($value): string { return "test string"; }],
            'mixed'    => [OptionMixed::class, static::SOME_VALUE, function ($value) { return new Mixed(static::SOME_VALUE); }],
        ];
    }
}
