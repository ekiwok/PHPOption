<?php

namespace Ekiwok\Optional\Test;

use Ekiwok\Optional\AnyOptional;
use Ekiwok\Optional\None;
use Ekiwok\Optional\NoSuchElementException;
use Ekiwok\Optional\Some;
use PHPUnit\Framework\TestCase;

class Stub
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

class AnyOptionalTest extends TestCase
{
    /**
     * @var AnyOptional
     */
    private $some;

    /**
     * @var AnyOptional
     */
    private $none;

    public function setUp()
    {
        $this->some = AnyOptional::of(new Stub('test'));
        $this->none = AnyOptional::of(null);
    }

    public function testOf()
    {
        $this->assertInstanceOf(AnyOptional::class, $this->some);
        $this->assertInstanceOf(Some::class, $this->some);

        $this->assertInstanceOf(AnyOptional::class, $this->none);
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

        $this->assertEquals(AnyOptional::of(new Stub(new Stub("test"))), $this->some->map($mapper));
        $this->assertEquals(AnyOptional::of(null), $this->none->map($mapper));
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
        $this->assertTrue($this->some->equals(AnyOptional::of(new Stub("test"))));
        $this->assertFalse($this->some->equals(AnyOptional::of(new Stub("something different"))));
        $this->assertFalse($this->some->equals(AnyOptional::of(null)));

        $this->assertTrue($this->none->equals(AnyOptional::of(null)));
        $this->assertFalse($this->none->equals(AnyOptional::of(new Stub("something different"))));
        $this->assertFalse($this->none->equals(AnyOptional::of("something")));
    }
}
