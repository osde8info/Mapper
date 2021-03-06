<?php
namespace ScriptFUSIONTest\Integration\Mapper\Strategy;

use ScriptFUSION\Mapper\InvalidConditionException;
use ScriptFUSION\Mapper\Mapper;
use ScriptFUSION\Mapper\Strategy\IfElse;

final class IfElseTest extends \PHPUnit_Framework_TestCase
{
    private $condition;

    public function setUp()
    {
        $this->condition = function ($data) {
            return array_key_exists('baz', $data) && $data['baz'] === 'qux';
        };
    }

    public function testIfElse()
    {
        $ifElse = (new IfElse($this->condition, 'foo', 'bar'))->setMapper(new Mapper);

        self::assertSame('foo', $ifElse(['baz' =>  'qux']));
        self::assertSame('bar', $ifElse(['baz' => 'quux']));
        self::assertSame('bar', $ifElse([]));
    }

    public function testOnlyIf()
    {
        $ifElse = (new IfElse($this->condition, 'foo'))->setMapper(new Mapper);

        self::assertSame('foo', $ifElse(['baz' =>  'qux']));
        self::assertNull($ifElse(['baz' => 'quux']));
        self::assertNull($ifElse([]));
    }

    public function testStrictness()
    {
        $this->setExpectedException(InvalidConditionException::class);

        $ifElse = (new IfElse(
            function () {
                return 1;
            },
            'foo',
            'bar'
        ))->setMapper(new Mapper);

        $ifElse([]);
    }
}
