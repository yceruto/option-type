<?php

declare(strict_types=1);

/*
 * This file is part of Option Type package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Std\Type;

use Std\Type\Exception\LogicOptionException;
use Std\Type\Exception\RuntimeOptionException;

use function Std\Type\Option\some;

/**
 * The Option type represents an optional value: every Option
 * is either Some and contains a value, or None, and does not.
 *
 * @template T
 */
interface Option
{
    /**
     * Returns `true` if the option is a {@see Some} value.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert($x->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * assert(!$x->isSome(), 'Expected $x not to be Some.');
     * ```
     *
     * @return bool `true` if the option is a {@see Some} value, otherwise `false`
     */
    public function isSome(): bool;

    /**
     * Returns `true` if the option is {@see None} value.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(!$x->isNone(), 'Expected $x not to be None.');
     *
     * $x = none();
     * assert($x->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @return bool `true` if the option is {@see None} value, otherwise `false`
     */
    public function isNone(): bool;

    /**
     * Matches the option with the provided callables and returns the result.
     *
     * @template U
     * @template V
     *
     * @param callable(T): U $some A callable that returns a value of type U
     * @param callable(): V  $none A callable that returns a value of type V
     *
     * @return U|V The result of the callable `$some` function if the option is {@see Some},
     *             otherwise the result of the callable `$none` function
     */
    public function match(callable $some, callable $none): mixed;

    /**
     * Returns the contained {@see Some} value, or throws an exception with custom message if the value is {@see None}.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->expect('A number must be provided.'), 'Expected $x to be 2.');
     *
     * $x = none();
     * $x->expect('A number.'); // throws RuntimeOptionException
     * ```
     *
     * @param string $message A custom error message to use in the RuntimeOptionException
     *
     * @return T The contained value
     *
     * @throws RuntimeOptionException If the value is {@see None} with a custom error message provided.
     *                                We recommend that `expect()` messages are used to describe the reason
     *                                you expect the `Option` should be {@see Some}.
     */
    public function expect(string $message): mixed;

    /**
     * Returns the contained {@see Some} value, or throws an exception if the value is {@see None}.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->unwrap(), 'Expected $x to be 2.');
     *
     * $x = none();
     * $x->unwrap(); // throws LogicOptionException
     * ```
     *
     * @return T The contained value
     *
     * @throws RuntimeOptionException If the value is {@see None}
     */
    public function unwrap(): mixed;

    /**
     * Returns the contained {@see Some} value or a provided default.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->unwrapOr(1), 'Expected $x to be 2.');
     *
     * $x = none();
     * assert(1 === $x->unwrapOr(1), 'Expected $x to be 1.');
     * ```
     *
     * @template U of T
     *
     * @param U $default A default value of type T
     *
     * @return T|U The contained value or the default
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained {@see Some} value or computes it from a closure.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->unwrapOrElse(fn () => 1), 'Expected $x to be 2.');
     *
     * $x = none();
     * assert(1 === $x->unwrapOrElse(fn () => 1), 'Expected $x to be 1.');
     * ```
     *
     * @template U
     *
     * @param callable(): U $fn A callable that returns a value of type U
     *
     * @return T|U The contained value or the result of the callable
     */
    public function unwrapOrElse(callable $fn): mixed;

    /**
     * Returns the contained {@see Some} value or throws an exception.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->unwrapOrThrow(), 'Expected $x to be 2.');
     *
     * $x = none();
     * $x->unwrapOrThrow(new UnknownNumberError()); // throws UnknownNumberError
     * ```
     *
     * @return T The contained value
     *
     * @throws \Throwable If the value is {@see None}
     */
    public function unwrapOrThrow(\Throwable $error): mixed;

    /**
     * Maps an `Option<T>` to `Option<U>` by applying a function to a contained value (if `Some`)
     * or returns `None` (if `None`).
     *
     * @template U
     *
     * @param callable(T): U $fn A callable that returns a value of type U
     *
     * @return self<null>|self<U> The mapped Option
     */
    public function map(callable $fn): self;

    /**
     * Returns the provided default result (if `None`),
     * or applies a function to the contained value (if `Some`).
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(4 === $x->mapOr(fn ($value) => $value * 2, 0), 'Expected $x to be 4.');
     *
     * $x = none();
     * assert(0 === $x->mapOr(fn ($value) => $value * 2, 0), 'Expected $x to be 0.');
     * ```
     *
     * @template U
     *
     * @param callable(T): U $fn      A callable that returns a value of type U
     * @param U              $default A default value of type U
     *
     * @return T|U The contained value or the default
     */
    public function mapOr(callable $fn, mixed $default): mixed;

    /**
     * Computes a default function result (if `None`), or
     * applies a different function to the contained value (if any).
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(4 === $x->mapOrElse(fn ($value) => $value * 2, fn () => 0), 'Expected $x to be 4.');
     *
     * $x = none();
     * assert(0 === $x->mapOrElse(fn ($value) => $value * 2, fn () => 0), 'Expected $x to be 0.');
     * ```
     *
     * @template U
     *
     * @param callable(T): U $fn      A callable that returns a value of type U
     * @param callable(): U  $default A callable that returns a default value of type U
     *
     * @return U The result of the callable
     */
    public function mapOrElse(callable $fn, callable $default): mixed;

    /**
     * Returns the option if it contains a value, otherwise returns `$option`.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * $y = some(3);
     * assert($x->or($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * $y = some(3);
     * assert($x->or($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * $y = none();
     * assert($x->or($y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param self<T> $option The option to return if the original is {@see None}
     *
     * @return self<T> The original option if it contains a value, otherwise `$option`
     */
    public function or(self $option): self;

    /**
     * Returns the option if it contains a value, otherwise calls `$fn` and
     * returns the result.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * $y = some(3);
     * assert($x->orElse(fn () => $y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * $y = some(3);
     * assert($x->orElse(fn () => $y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * $y = none();
     * assert($x->orElse(fn () => $y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param callable(): self<T> $fn A callable that returns an Option
     *
     * @return self<T> The original option if it contains a value, otherwise the result of the callable
     */
    public function orElse(callable $fn): self;

    /**
     * Returns {@see Some} if exactly one of `$this`, `$option` is {@see Some}, otherwise returns {@see None}.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * $y = some(3);
     * assert($x->xor($y)->isNone(), 'Expected $x to be None.');
     *
     * $x = none();
     * $y = some(3);
     * assert($x->xor($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * $y = none();
     * assert($x->xor($y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param self<T> $option The option to compare with
     *
     * @return self<T>|self<null>
     */
    public function xor(self $option): self;

    /**
     * Returns {@see None} if the option is {@see None}, otherwise returns `$option`.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * $y = some(3);
     * assert($x->and($y)->isSome(), 'Expected $x and $y to be Some.');
     *
     * $x = none();
     * $y = some(3);
     * assert($x->and($y)->isNone(), 'Expected $x to be None.');
     *
     * $x = none();
     * $y = none();
     * assert($x->and($y)->isNone(), 'Expected $x and $y to be None.');
     * ```
     *
     * @param self<T> $option The option to compare with
     *
     * @return self<T>|self<null> `$option` if the original is {@see None}, otherwise the original option
     */
    public function and(self $option): self;

    /**
     * Returns {@see None} if the Option is {@see None}, otherwise calls `$fn` with
     * the wrapped value and returns the result.
     *
     * Some languages call this method flatmap.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2)->andThen(fn ($value) => some($value * 2));
     * assert(4 === $x->unwrap(), 'Expected $x to be 4.');
     *
     * $x = none()->andThen(fn ($value) => some($value * 2));
     * assert($x->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @template U
     *
     * @param callable(T): self<U> $fn A callable that returns an Option
     *
     * @return self<null>|self<U> The result of the callable
     */
    public function andThen(callable $fn): self;

    /**
     * Returns an iterator over the possibly contained value.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert([2] === iterator_to_array($x->iterate()), 'Expected to be [2].');
     *
     * $x = none();
     * assert([] === iterator_to_array($x->iterate()), 'Expected to be [].');
     * ```
     *
     * @return iterable<T> An iterator over the possibly contained value
     */
    public function iterate(): iterable;

    /**
     * Returns {@see None} if the Option is {@see None}, otherwise calls `$predicate`
     * with the wrapped value and returns:
     *
     * - {@see Some}(v) If `$predicate` returns `true` (where `v` is the wrapped value), and
     * - {@see None} if `$predicate` returns `false`.
     *
     * <b>Examples</b>
     * ```
     * $isEven = fn (int $value): bool => 0 === $value % 2;
     *
     * assert(none()->filter($isEven)->isNone(), 'Expected to be true.');
     * assert(some(3)->filter($isEven)->isNone(), 'Expected to be true.');
     * assert(some(2)->filter($isEven)->isSome(), 'Expected to be true.');
     * ```
     *
     * @param callable(T): bool $predicate A callable that returns a boolean
     *
     * @return self<T> {@see Some} if it's {@see Some} option and the predicate is `true`, otherwise {@see None}
     */
    public function filter(callable $predicate): self;

    /**
     * Compares the option with the provided option and returns `true` if they are equal.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * $y = some(2);
     * assert($x->equals($y), 'Expected $x to be equal to $y.');
     *
     * $x = some(2);
     * $y = some(3);
     * assert(!$x->equals($y), 'Expected $x not to be equal to $y.');
     *
     * $x = none();
     * $y = none();
     * assert($x->equals($y), 'Expected $x to be equal to $y.');
     * ```
     *
     * @param self<T> $option The option to compare with
     *
     * @return bool `true` if the wrapped value is the same value as `$option`, otherwise `false`
     */
    public function equals(self $option): bool;

    /**
     * Converts from `Option<Option<T>>` to `Option<T>`.
     *
     * <b>Examples</b>
     * ```
     * $x = some(some(2));
     * assert(2 === $x->flatten()->unwrap(), 'Expected to be 2.');
     *
     * $x = some(none());
     * assert($x->flatten()->isNone(), 'Expected to be None.');
     *
     * $x = none();
     * assert($x->flatten()->isNone(), 'Expected to be None.');
     *
     * # Flattening only removes one level of nesting at a time:
     * $x = some(some(some(2)));
     * assert(some(some(2)) == $x->flatten(), 'Expected to be Option<Option<T>>.');
     * assert(some(2) == $x->flatten()->flatten(), 'Expected to be Option<T>.');
     * ```
     *
     * @return self<T> The flattened Option
     *
     * @throws LogicOptionException If the value is not an Option
     */
    public function flatten(): self;

    /**
     * Returns a copy of the option.
     *
     * @return self<T> A copy of the option
     */
    public function clone(): self;
}
