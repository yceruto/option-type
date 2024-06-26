<?php

declare(strict_types=1);

/*
 * This file is part of Option Type package.
 *
 * (c) Yonel Ceruto <patch@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Std\Type;

use function Std\Type\Option\some;

/**
 * The Option type represents an optional value: every Option
 * is either Some and contains a value, or None, and does not.
 *
 * @template T
 */
final readonly class Option
{
    /**
     * Some value.
     *
     * Also see {@see some()} for a shorter way to create a Some Option.
     *
     * @param T $value A value of type T
     *
     * @return self<T> Some Option
     */
    public static function some(mixed $value): self
    {
        if (null === $value) {
            throw new \LogicException('Cannot create a Some option with a null value, use None instead.');
        }

        return new self($value);
    }

    /**
     * No value.
     *
     * Also see {@see none()} for a shorter way to create a Some Option.
     *
     * @return self<null> None Option
     */
    public static function none(): self
    {
        return new self(null);
    }

    /**
     * Returns an `Option` with the specified value.
     *
     * If the value is `null`, returns `None`, otherwise returns `Some`.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::from(2);
     * assert($x->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::from(null);
     * assert($x->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param T $value A value of type T
     *
     * @return self<T>|self<null> Some or None Option
     */
    public static function from(mixed $value): self
    {
        return null === $value ? self::none() : self::some($value);
    }

    /**
     * Returns `true` if the option is a {@see some} value.
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
     * @return bool `true` if the option is a {@see some} value, otherwise `false`
     */
    public function isSome(): bool
    {
        return null !== $this->value;
    }

    /**
     * Returns `true` if the option is a {@see none} value.
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
     * @return bool `true` if the option is a {@see none} value, otherwise `false`
     */
    public function isNone(): bool
    {
        return null === $this->value;
    }

    /**
     * Matches the option with the provided callables and returns the result.
     *
     * @template U
     * @template V
     *
     * @param callable(T): U $some A callable that returns a value of type U
     * @param callable(): V  $none A callable that returns a value of type V
     *
     * @return U|V The result of the callable `$some` function if the option is {@see some},
     *             otherwise the result of the callable `$none` function
     */
    public function match(callable $some, callable $none): mixed
    {
        return $this->isSome() ? $some($this->value) : $none();
    }

    /**
     * Returns the contained {@see some} value, or throws an exception with custom message if the value is a {@see none}.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->expect('A number.'), 'Expected $x to be 2.');
     *
     * $x = none();
     * $x->expect('A number.'); // throws LogicException
     * ```
     *
     * @param string $message A custom error message to use in the LogicException
     *
     * @return T The contained value
     *
     * @throws \LogicException If the value is a {@see none} with a custom error message provided.
     *                         We recommend that `expect()` messages are used to describe the reason
     *                         you expect the `Option` should be {@see some}.
     */
    public function expect(string $message): mixed
    {
        if ($this->isNone()) {
            throw new \LogicException($message);
        }

        return $this->value;
    }

    /**
     * Returns the contained {@see some} value, or throws an exception if the value is a {@see none}.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->unwrap(), 'Expected $x to be 2.');
     *
     * $x = none();
     * $x->unwrap(); // throws LogicException
     * ```
     *
     * @return T The contained value
     *
     * @throws \LogicException If the value is a {@see none}
     */
    public function unwrap(): mixed
    {
        if ($this->isNone()) {
            throw new \LogicException('Called Option::unwrap() on a None value.');
        }

        return $this->value;
    }

    /**
     * Returns the contained {@see some} value or a provided default.
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
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value ?? $default;
    }

    /**
     * Returns the contained {@see some} value or computes it from a closure.
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
    public function unwrapOrElse(callable $fn): mixed
    {
        return $this->value ?? $fn();
    }

    /**
     * Returns the contained {@see some} value or throws an exception.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * assert(2 === $x->unwrapOrThrow(), 'Expected $x to be 2.');
     *
     * $x = none();
     * $x->unwrapOrThrow(new UnknownNumberError()); // throws LogicException
     * ```
     *
     * @return T The contained value
     *
     * @throws \Throwable If the value is a {@see none}
     */
    public function unwrapOrThrow(\Throwable $error): mixed
    {
        if ($this->isNone()) {
            throw $error;
        }

        return $this->value;
    }

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
    public function map(callable $fn): self
    {
        if ($this->isNone()) {
            return $this;
        }

        if (null === $value = $fn($this->value)) {
            return self::none();
        }

        return some($value);
    }

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
    public function mapOr(callable $fn, mixed $default): mixed
    {
        if ($this->isNone()) {
            return $default;
        }

        return $fn($this->value);
    }

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
    public function mapOrElse(callable $fn, callable $default): mixed
    {
        if ($this->isNone()) {
            return $default();
        }

        return $fn($this->value);
    }

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
     * @param self<T> $option The option to return if the original is {@see none}
     *
     * @return self<T> The original option if it contains a value, otherwise `$option`
     */
    public function or(self $option): self
    {
        return $this->isSome() ? $this : $option;
    }

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
    public function orElse(callable $fn): self
    {
        return $this->isSome() ? $this : $fn();
    }

    /**
     * Returns {@see some} if exactly one of `$this`, `$option` is {@see some}, otherwise returns {@see none}.
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
    public function xor(self $option): self
    {
        return $this->isSome() !== $option->isSome() ? $this->or($option) : self::none();
    }

    /**
     * Returns {@see none} if the option is {@see none}, otherwise returns `$option`.
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
     * @return self<T>|self<null> `$option` if the original is {@see none}, otherwise the original option
     */
    public function and(self $option): self
    {
        return $this->isSome() ? $option : $this;
    }

    /**
     * Returns {@see none} if the Option is {@see none}, otherwise calls `$fn` with
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
    public function andThen(callable $fn): self
    {
        if ($this->isNone()) {
            return $this;
        }

        return $fn($this->value);
    }

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
    public function iterate(): iterable
    {
        return new \ArrayIterator((array) $this->value);
    }

    /**
     * Returns {@see none} if the Option is {@see none}, otherwise calls `$predicate`
     * with the wrapped value and returns:
     *
     * - {@see some}(v) If `$predicate` returns `true` (where `v` is the wrapped value), and
     * - {@see none} if `$predicate` returns `false`.
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
     * @return self<T>|self<null> {@see some} if it's {@see some} option and the predicate is `true`, otherwise {@see none}
     */
    public function filter(callable $predicate): Option
    {
        if ($this->isSome() && $predicate($this->value)) {
            return $this;
        }

        return self::none();
    }

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
    public function equals(self $option): bool
    {
        return $this->value === $option->value;
    }

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
     * @return self<null>|self<T> The flattened Option
     *
     * @throws \LogicException If the value is not an Option
     */
    public function flatten(): self
    {
        if ($this->isNone()) {
            return $this;
        }

        if ($this->value instanceof self) {
            return $this->value;
        }

        throw new \LogicException('Cannot flatten a non-Option value.');
    }

    /**
     * Returns a copy of the option.
     *
     * @return self<T> A copy of the option
     */
    public function clone(): self
    {
        return new self($this->value);
    }

    /**
     * @param T $value A value of type T
     */
    private function __construct(
        private mixed $value,
    ) {
    }
}
