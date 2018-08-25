<?php

namespace Ekiwok\Optional\Test;

use function Ekiwok\Function1\of;
use Ekiwok\Optional\Mixed;
use Ekiwok\Optional\None;
use Ekiwok\Optional\NoSuchElementException;
use Ekiwok\Optional\OptionMixed;
use Ekiwok\Optional\OptionString;
use Ekiwok\Optional\Some;
use PHPUnit\Framework\TestCase;

class OptionStringTest extends TestCase
{
    const TESTED_CLASS = OptionString::class;
    const SOME_VALUE = "test";
    const OR_ELSE_VALUE = "supplied value";

    /**
     * @var OptionString
     */
    protected $some;

    /**
     * @var OptionString
     */
    protected $none;

    public function setUp()
    {
        $this->some = of(self::TESTED_CLASS, self::SOME_VALUE);
        $this->none = of(self::TESTED_CLASS, null);
    }

    public function testOf()
    {
        $this->assertInstanceOf(self::TESTED_CLASS, $this->some);
        $this->assertInstanceOf(Some::class, $this->some);

        $this->assertInstanceOf(self::TESTED_CLASS, $this->none);
        $this->assertInstanceOf(None::class, $this->none);
    }

    public function testGet()
    {
        $this->assertEquals(self::SOME_VALUE, $this->some->get());

        $this->expectException(NoSuchElementException::class);
        $this->none->get();
    }

    public function testOrElse()
    {
        $this->assertEquals(self::SOME_VALUE, $this->some->orElse(self::OR_ELSE_VALUE));
        $this->assertEquals(self::OR_ELSE_VALUE, $this->none->orElse(self::OR_ELSE_VALUE));
    }

    public function testOrElseGet()
    {
        $supplier = function () {
            return self::OR_ELSE_VALUE;
        };

        $this->assertEquals(self::SOME_VALUE, $this->some->orElseGet($supplier));
        $this->assertEquals(self::OR_ELSE_VALUE, $this->none->orElseGet($supplier));
    }

    public function testOrElseThrow()
    {
        $supplier = function () {
            return new \RuntimeException();
        };

        $this->assertEquals(self::SOME_VALUE, $this->some->orElseThrow($supplier));

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
        $this->assertTrue($this->some->equals(of(self::TESTED_CLASS, self::SOME_VALUE)));
        $this->assertFalse($this->some->equals(of(self::TESTED_CLASS, self::OR_ELSE_VALUE)));
        $this->assertFalse($this->some->equals(of(self::TESTED_CLASS, null)));

        $this->assertTrue($this->none->equals(of(self::TESTED_CLASS, null)));
        $this->assertFalse($this->none->equals(of(self::TESTED_CLASS, self::SOME_VALUE)));
        $this->assertFalse($this->none->equals(of(self::TESTED_CLASS, self::OR_ELSE_VALUE)));
    }

    public function dataProviderTestMap()
    {
        return [
            'identity' => [self::TESTED_CLASS, self::SOME_VALUE,  function ($value) { return self::SOME_VALUE; }],
            'string'   => [OptionString::class, "test string", function ($value): string { return "test string"; }],
            'mixed'    => [OptionMixed::class, self::SOME_VALUE, function ($value) { return new Mixed(self::SOME_VALUE); }],
        ];
    }
}

