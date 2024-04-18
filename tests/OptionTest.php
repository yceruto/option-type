<?php

namespace Std\Type\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Std\Type\Option;

use function Std\Type\Option\some;
use function Std\Type\Option\none;

#[CoversClass(Option::class)]
class OptionTest extends TestCase
{
    public function testSome(): void
    {
        $opt = some(23);

        self::assertTrue($opt->isSome());
    }

    public function testInvalidSome(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot create a Some option with a null value, use None instead.');
        some(null);
    }

    public function testNone(): void
    {
        $opt = none();

        self::assertTrue($opt->isNone());
    }

    public function testFrom(): void
    {
        $opt1 = Option::from(23);
        $opt2 = Option::from(null);

        self::assertEquals(some(23), $opt1);
        self::assertEquals(none(), $opt2);
    }

    public function testExpect(): void
    {
        $opt = some(23);

        self::assertSame(23, $opt->expect('This should not throw an exception.'));
    }

    public function testUnwrap(): void
    {
        $opt = some(23);

        self::assertSame(23, $opt->unwrap());
    }

    public function testUnwrapThrowsException(): void
    {
        $opt = none();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called Option::unwrap() on a None value.');
        $opt->unwrap();
    }

    public function testExpectThrowsException(): void
    {
        $opt = none();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This should throw an exception.');
        $opt->expect('This should throw an exception.');
    }

    public function testUnwrapOr(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        self::assertSame(23, $opt1->unwrapOr(1));
        self::assertSame(1, $opt2->unwrapOr(1));
    }

    public function testUnwrapOrElse(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        self::assertSame(23, $opt1->unwrapOrElse(fn () => 1));
        self::assertSame(1, $opt2->unwrapOrElse(fn () => 1));
    }

    public function testUnwrapOrThrow(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        self::assertSame(23, $opt1->unwrapOrThrow(new \RuntimeException('This should not throw an exception.')));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This should throw an exception.');
        $opt2->unwrapOrThrow(new \RuntimeException('This should throw an exception.'));
    }

    public function testMap(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $mappedOpt1 = $opt1->map(fn ($value) => $value * 2);
        $mappedOpt2 = $opt2->map(fn ($value) => $value * 2);

        self::assertSame(46, $mappedOpt1->unwrap());
        self::assertTrue($mappedOpt2->isNone());
    }

    public function testMapOr(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $mappedOpt1 = $opt1->mapOr(fn ($value) => $value * 2, 1);
        $mappedOpt2 = $opt2->mapOr(fn ($value) => $value * 2, 1);

        self::assertSame(46, $mappedOpt1);
        self::assertSame(1, $mappedOpt2);
    }

    public function testMapOrElse(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $mappedOpt1 = $opt1->mapOrElse(fn ($value) => $value * 2, fn () => 1);
        $mappedOpt2 = $opt2->mapOrElse(fn ($value) => $value * 2, fn () => 1);

        self::assertSame(46, $mappedOpt1);
        self::assertSame(1, $mappedOpt2);
    }

    public function testOr(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $orOpt1 = $opt1->or(some(42));
        $orOpt2 = $opt1->or(none());
        $orOpt3 = $opt2->or(some(42));
        $orOpt4 = $opt2->or(none());

        self::assertSame(23, $orOpt1->unwrap());
        self::assertSame(23, $orOpt2->unwrap());
        self::assertSame(42, $orOpt3->unwrap());
        self::assertTrue($orOpt4->isNone());
    }

    public function testOrElse(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $orElseOpt1 = $opt1->orElse(fn () => some(42));
        $orElseOpt2 = $opt1->orElse(fn () => none());
        $orElseOpt3 = $opt2->orElse(fn () => some(42));
        $orElseOpt4 = $opt2->orElse(fn () => none());

        self::assertSame(23, $orElseOpt1->unwrap());
        self::assertSame(23, $orElseOpt2->unwrap());
        self::assertSame(42, $orElseOpt3->unwrap());
        self::assertTrue($orElseOpt4->isNone());
    }

    public function testXor(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $xorOpt1 = $opt1->xor(some(42));
        $xorOpt2 = $opt1->xor(none());
        $xorOpt3 = $opt2->xor(some(42));
        $xorOpt4 = $opt2->xor(none());

        self::assertTrue($xorOpt1->isNone());
        self::assertSame(23, $xorOpt2->unwrap());
        self::assertSame(42, $xorOpt3->unwrap());
        self::assertTrue($xorOpt4->isNone());
    }

    public function testAnd(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $andOpt1 = $opt1->and(some(42));
        $andOpt2 = $opt1->and(none());
        $andOpt3 = $opt2->and(some(42));
        $andOpt4 = $opt2->and(none());

        self::assertTrue($andOpt1->isSome());
        self::assertTrue($andOpt2->isNone());
        self::assertTrue($andOpt3->isNone());
        self::assertTrue($andOpt4->isNone());
    }

    public function testAndThen(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $andThenOpt1 = $opt1->andThen(fn ($value) => some($value * 2));
        $andThenOpt2 = $opt2->andThen(fn ($value) => some($value * 2));

        self::assertSame(46, $andThenOpt1->unwrap());
        self::assertTrue($andThenOpt2->isNone());
    }

    public function testIterate(): void
    {
        $opt1 = some(23);
        $opt2 = none();
        $opt3 = some(['a', 'b', 'c']);

        foreach ($opt1->iterate() as $value) {
            self::assertSame(23, $value);
        }

        foreach ($opt2->iterate() as $_) {
            self::fail('This should not be called.');
        }

        self::assertCount(3, $opt3->iterate());
    }

    public function testFilter(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $filteredOpt1 = $opt1->filter(fn ($value) => $value > 10);
        $filteredOpt2 = $opt1->filter(fn ($value) => $value > 30);
        $filteredOpt3 = $opt2->filter(fn ($value) => $value > 10);

        self::assertTrue($filteredOpt1->isSome());
        self::assertTrue($filteredOpt2->isNone());
        self::assertTrue($filteredOpt3->isNone());
    }

    public function testEquals(): void
    {
        $opt1 = some(23);
        $opt2 = some(23);
        $opt3 = some(42);
        $opt4 = none();
        $opt5 = none();

        self::assertTrue($opt1->equals($opt2));
        self::assertFalse($opt1->equals($opt3));
        self::assertFalse($opt1->equals($opt4));
        self::assertTrue($opt4->equals($opt5));
    }

    public function testFlatten(): void
    {
        $opt1 = some(some(23));
        $opt2 = some(none());
        $opt3 = none();
        $opt4 = some(some(some(23)));

        self::assertEquals(some(23), $opt1->flatten());
        self::assertEquals(none(), $opt2->flatten());
        self::assertEquals(none(), $opt3->flatten());

        self::assertEquals(some(some(23)), $opt4->flatten());
        self::assertEquals(some(23), $opt4->flatten()->flatten());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot flatten a non-Option value.');
        $opt4->flatten()->flatten()->flatten();
    }

    public function testMatch(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $result1 = $opt1->match(
            some: fn ($value) => $value * 2,
            none: fn () => 0,
        );

        $result2 = $opt2->match(
            some: fn ($value) => $value * 2,
            none: fn () => 0,
        );

        self::assertSame(46, $result1);
        self::assertSame(0, $result2);
    }

    public function testClone(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        $clonedOpt1 = $opt1->clone();
        $clonedOpt2 = $opt2->clone();

        self::assertTrue($opt1->equals($clonedOpt1));
        self::assertTrue($opt2->equals($clonedOpt2));
    }

    public function testFunctions(): void
    {
        $opt1 = some(23);
        $opt2 = none();

        self::assertTrue($opt1->isSome());
        self::assertTrue($opt2->isNone());
    }
}
