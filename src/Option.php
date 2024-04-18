<?php

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
     * @return self<T>
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
     * @return self<null>
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
     * @return self<T>|self<null>
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
     */
    public function isNone(): bool
    {
        return null === $this->value;
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
     * @return T The contained value
     *
     * @throws \LogicException If the value is a {@see none} with a custom error message provided.
     *                           We recommend that `expect()` messages are used to describe the reason
     *                           you expect the `Option` should be {@see some}.
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
     * @return T
     *
     * @throws \LogicException
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
     * @template TDefault of T
     *
     * @param TDefault $default
     *
     * @return T|TDefault
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
     * @param callable(): U $fn
     *
     * @return T|U
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
     * @return T
     *
     * @throws \Throwable
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
     * @param callable(T): U $fn
     *
     * @return self<null>|self<U>
     */
    public function map(callable $fn): self
    {
        if ($this->isNone()) {
            return self::none();
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
     * @param callable(T): U $fn
     * @param U $default
     *
     * @return T|U
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
     * @param callable(T): U $fn
     * @param callable(): U $default
     *
     * @return U
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
     * @return self<T>
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
     * @param callable(): self<T> $fn A closure that returns an Option
     *
     * @return self<T>
     */
    public function orElse(callable $fn): self
    {
        return $this->isSome() ? $this : $fn();
    }

    /**
     * Returns {@see some} if exactly one of `self`, `$option` is {@see some}, otherwise returns {@see none}.
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
     * assert($x->and($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = none();
     * $y = some(3);
     * assert($x->and($y)->isNone(), 'Expected $x to be None.');
     *
     * $x = none();
     * $y = none();
     * assert($x->and($y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param self<T> $option The option to compare with
     *
     * @return self<T>|self<null>
     */
    public function and(self $option): self
    {
        return $this->isSome() ? $option : self::none();
    }

    /**
     * Returns {@see none} if the Option is {@see none}, otherwise calls `$fn` with
     * the wrapped value and returns the result.
     *
     * Some languages call this method flatmap.
     *
     * <b>Examples</b>
     * ```
     * $x = some(2);
     * $y = $x->andThen(fn ($value) => some($value * 2));
     * assert(4 === $y->unwrap(), 'Expected $y to be 4.');
     *
     * $x = none();
     * $y = $x->andThen(fn ($value) => some($value * 2));
     * assert($y->isNone(), 'Expected $y to be None.');
     * ```
     *
     * @template U
     *
     * @param callable(T): self<U> $fn
     *
     * @return self<null>|self<U>
     */
    public function andThen(callable $fn): self
    {
        if ($this->isNone()) {
            return self::none();
        }

        return $fn($this->value);
    }

    /**
     * Returns an iterator over the possibly contained value.
     *
     * The iterator yields the values if the `Option` is a `Some`, otherwise none.
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
     * @return \ArrayIterator<int|string, T>
     */
    public function iterate(): \ArrayIterator
    {
        if ($this->isSome()) {
            /** @var \ArrayIterator<int|string, T> */
            return new \ArrayIterator((array) $this->value);
        }

        return new \ArrayIterator();
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
     * $isEven = fn ($value) => 0 === $value % 2;
     *
     * assert(none(->filter($isEven)->isSome(), 'Expected to be false.');
     * assert(some(3)->filter($isEven)->isNone(), 'Expected to be false.');
     * assert(some(2)->filter($isEven)->isSome(), 'Expected to be true.');
     * ```
     *
     * @param callable(T): bool $predicate A closure that returns a boolean
     *
     * @return self<T>|self<null>
     */
    public function filter(callable $predicate): Option
    {
        if ($this->isNone() || $predicate($this->value)) {
            return $this;
        }

        return self::none();
    }

    /**
     * Returns `true` if the `self` value is the same value as `$option`.
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
     * @return self<null>|self<T>
     */
    public function flatten(): self
    {
        if ($this->isNone()) {
            return self::none();
        }

        if ($this->value instanceof self) {
            return $this->value;
        }

        throw new \LogicException('Cannot flatten a non-Option value.');
    }

    /**
     * Returns a copy of the option.
     *
     * @return self<T>
     */
    public function clone(): self
    {
        return new self($this->value);
    }

    /**
     * @param T $value
     */
    private function __construct(
        private mixed $value,
    ) {
    }
}
