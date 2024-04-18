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
        $opt = Option::some(23);

        self::assertTrue($opt->isSome());
    }

    public function testInvalidSome(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot create a Some option with a null value, use None instead.');
        Option::some(null);
    }

    public function testNone(): void
    {
        $opt = Option::none();

        self::assertTrue($opt->isNone());
    }

    public function testFrom(): void
    {
        $opt1 = Option::from(23);
        $opt2 = Option::from(null);

        self::assertEquals(Option::some(23), $opt1);
        self::assertEquals(Option::none(), $opt2);
    }

    public function testExpect(): void
    {
        $opt = Option::some(23);

        self::assertSame(23, $opt->expect('This should not throw an exception.'));
    }

    public function testUnwrap(): void
    {
        $opt = Option::some(23);

        self::assertSame(23, $opt->unwrap());
    }

    public function testUnwrapThrowsException(): void
    {
        $opt = Option::none();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called Option::unwrap() on a None value.');
        $opt->unwrap();
    }

    public function testExpectThrowsException(): void
    {
        $opt = Option::none();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This should throw an exception.');
        $opt->expect('This should throw an exception.');
    }

    public function testUnwrapOr(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        self::assertSame(23, $opt1->unwrapOr(1));
        self::assertSame(1, $opt2->unwrapOr(1));
    }

    public function testUnwrapOrElse(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        self::assertSame(23, $opt1->unwrapOrElse(fn () => 1));
        self::assertSame(1, $opt2->unwrapOrElse(fn () => 1));
    }

    public function testMap(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $mappedOpt1 = $opt1->map(fn ($value) => $value * 2);
        $mappedOpt2 = $opt2->map(fn ($value) => $value * 2);

        self::assertSame(46, $mappedOpt1->unwrap());
        self::assertTrue($mappedOpt2->isNone());
    }

    public function testMapOr(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $mappedOpt1 = $opt1->mapOr(fn ($value) => $value * 2, 1);
        $mappedOpt2 = $opt2->mapOr(fn ($value) => $value * 2, 1);

        self::assertSame(46, $mappedOpt1);
        self::assertSame(1, $mappedOpt2);
    }

    public function testMapOrElse(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $mappedOpt1 = $opt1->mapOrElse(fn ($value) => $value * 2, fn () => 1);
        $mappedOpt2 = $opt2->mapOrElse(fn ($value) => $value * 2, fn () => 1);

        self::assertSame(46, $mappedOpt1);
        self::assertSame(1, $mappedOpt2);
    }

    public function testOr(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $orOpt1 = $opt1->or(Option::some(42));
        $orOpt2 = $opt1->or(Option::none());
        $orOpt3 = $opt2->or(Option::some(42));
        $orOpt4 = $opt2->or(Option::none());

        self::assertSame(23, $orOpt1->unwrap());
        self::assertSame(23, $orOpt2->unwrap());
        self::assertSame(42, $orOpt3->unwrap());
        self::assertTrue($orOpt4->isNone());
    }

    public function testOrElse(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $orElseOpt1 = $opt1->orElse(fn () => Option::some(42));
        $orElseOpt2 = $opt1->orElse(fn () => Option::none());
        $orElseOpt3 = $opt2->orElse(fn () => Option::some(42));
        $orElseOpt4 = $opt2->orElse(fn () => Option::none());

        self::assertSame(23, $orElseOpt1->unwrap());
        self::assertSame(23, $orElseOpt2->unwrap());
        self::assertSame(42, $orElseOpt3->unwrap());
        self::assertTrue($orElseOpt4->isNone());
    }

    public function testXor(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $xorOpt1 = $opt1->xor(Option::some(42));
        $xorOpt2 = $opt1->xor(Option::none());
        $xorOpt3 = $opt2->xor(Option::some(42));
        $xorOpt4 = $opt2->xor(Option::none());

        self::assertTrue($xorOpt1->isNone());
        self::assertSame(23, $xorOpt2->unwrap());
        self::assertSame(42, $xorOpt3->unwrap());
        self::assertTrue($xorOpt4->isNone());
    }

    public function testAnd(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $andOpt1 = $opt1->and(Option::some(42));
        $andOpt2 = $opt1->and(Option::none());
        $andOpt3 = $opt2->and(Option::some(42));
        $andOpt4 = $opt2->and(Option::none());

        self::assertSame(42, $andOpt1->unwrap());
        self::assertTrue($andOpt2->isNone());
        self::assertTrue($andOpt3->isNone());
        self::assertTrue($andOpt4->isNone());
    }

    public function testAndThen(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $andThenOpt1 = $opt1->andThen(fn ($value) => Option::some($value * 2));
        $andThenOpt2 = $opt2->andThen(fn ($value) => Option::some($value * 2));

        self::assertSame(46, $andThenOpt1->unwrap());
        self::assertTrue($andThenOpt2->isNone());
    }

    public function testIterate(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();
        $opt3 = Option::some(['a', 'b', 'c']);

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
        $opt1 = Option::some(23);
        $opt2 = Option::none();

        $filteredOpt1 = $opt1->filter(fn ($value) => $value > 10);
        $filteredOpt2 = $opt1->filter(fn ($value) => $value > 30);
        $filteredOpt3 = $opt2->filter(fn ($value) => $value > 10);

        self::assertSame(23, $filteredOpt1->unwrap());
        self::assertTrue($filteredOpt2->isNone());
        self::assertTrue($filteredOpt3->isNone());
    }

    public function testEquals(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::some(23);
        $opt3 = Option::some(42);
        $opt4 = Option::none();
        $opt5 = Option::none();

        self::assertTrue($opt1->equals($opt2));
        self::assertFalse($opt1->equals($opt3));
        self::assertFalse($opt1->equals($opt4));
        self::assertTrue($opt4->equals($opt5));
    }

    public function testFlatten(): void
    {
        $opt1 = Option::some(Option::some(23));
        $opt2 = Option::some(Option::none());
        $opt3 = Option::none();
        $opt4 = Option::some(Option::some(Option::some(23)));

        self::assertEquals(Option::some(23), $opt1->flatten());
        self::assertEquals(Option::none(), $opt2->flatten());
        self::assertEquals(Option::none(), $opt3->flatten());

        self::assertEquals(Option::some(Option::some(23)), $opt4->flatten());
        self::assertEquals(Option::some(23), $opt4->flatten()->flatten());
    }

    public function testClone(): void
    {
        $opt1 = Option::some(23);
        $opt2 = Option::none();

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
