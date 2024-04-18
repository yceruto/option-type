<?php

namespace Type;

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
     * @param T $value A value of type T
     *
     * @return self<T>
     */
    public static function Some(mixed $value): self
    {
        if (null === $value) {
            throw new \LogicException('Cannot create a Some option with a null value, use None instead.');
        }

        return new self($value);
    }

    /**
     * No value.
     *
     * @return self<null>
     */
    public static function None(): self
    {
        return new self(null);
    }

    /**
     * Returns `true` if the option is a {@see Some} value.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert($x->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * assert(!$x->isSome(), 'Expected $x not to be Some.');
     * ```
     */
    public function isSome(): bool
    {
        return null !== $this->value;
    }

    /**
     * Returns `true` if the option is a {@see None} value.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert(!$x->isNone(), 'Expected $x not to be None.');
     *
     * $x = Option::None();
     * assert($x->isNone(), 'Expected $x to be None.');
     * ```
     */
    public function isNone(): bool
    {
        return null === $this->value;
    }

    /**
     * Returns the contained {@see Some} value, or throws an exception with custom message if the value is a {@see None}.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert(2 === $x->expect('A number.'), 'Expected $x to be 2.');
     *
     * $x = Option::None();
     * $x->expect('A number.'); // throws LogicException
     * ```
     *
     * @return T The contained value
     *
     * @throws \LogicException If the value is a {@see None} with a custom error message provided.
     *                           We recommend that `expect()` messages are used to describe the reason
     *                           you expect the `Option` should be {@see Some}.
     */
    public function expect(string $message): mixed
    {
        if ($this->isNone()) {
            throw new \LogicException($message);
        }

        return $this->value;
    }

    /**
     * Returns the contained {@see Some} value, or throws an exception if the value is a {@see None}.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert(2 === $x->unwrap(), 'Expected $x to be 2.');
     *
     * $x = Option::None();
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
     * Returns the contained {@see Some} value or a provided default.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert(2 === $x->unwrapOr(1), 'Expected $x to be 2.');
     *
     * $x = Option::None();
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
     * Returns the contained {@see Some} value or computes it from a closure.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert(2 === $x->unwrapOrElse(fn() => 1), 'Expected $x to be 2.');
     *
     * $x = Option::None();
     * assert(1 === $x->unwrapOrElse(fn() => 1), 'Expected $x to be 1.');
     * ```
     *
     * @template U
     *
     * @param \Closure(): U $fn
     *
     * @return T|U
     */
    public function unwrapOrElse(\Closure $fn): mixed
    {
        return $this->value ?? $fn();
    }

    /**
     * Maps an `Option<T>` to `Option<U>` by applying a function to a contained value (if `Some`)
     * or returns `None` (if `None`).
     *
     * @template U
     *
     * @param \Closure(T): U $fn
     *
     * @return self<U>
     */
    public function map(\Closure $fn): self
    {
        if ($this->isNone()) {
            return self::None();
        }

        return self::Some($fn($this->value));
    }

    /**
     * Returns the provided default result (if `None`),
     * or applies a function to the contained value (if `Some`).
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * assert(4 === $x->mapOr(fn($value) => $value * 2, 0), 'Expected $x to be 4.');
     *
     * $x = Option::None();
     * assert(0 === $x->mapOr(fn($value) => $value * 2, 0), 'Expected $x to be 0.');
     * ```
     *
     * @template U
     *
     * @param \Closure(T): U $fn
     * @param U $default
     *
     * @return T|U
     */
    public function mapOr(\Closure $fn, mixed $default): mixed
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
     * $x = Option::Some(2);
     * assert(4 === $x->mapOrElse(fn($value) => $value * 2, fn() => 0), 'Expected $x to be 4.');
     *
     * $x = Option::None();
     * assert(0 === $x->mapOrElse(fn($value) => $value * 2, fn() => 0), 'Expected $x to be 0.');
     * ```
     *
     * @template U
     *
     * @param \Closure(T): U $fn
     * @param \Closure(): U $default
     *
     * @return U
     */
    public function mapOrElse(\Closure $fn, \Closure $default): mixed
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
     * $x = Option::Some(2);
     * $y = Option::Some(3);
     * assert($x->or($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * $y = Option::Some(3);
     * assert($x->or($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * $y = Option::None();
     * assert($x->or($y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param self<T> $option The option to return if the original is {@see None}
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
     * $x = Option::Some(2);
     * $y = Option::Some(3);
     * assert($x->orElse(fn() => $y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * $y = Option::Some(3);
     * assert($x->orElse(fn() => $y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * $y = Option::None();
     * assert($x->orElse(fn() => $y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param \Closure(): self<T> $fn A closure that returns an Option
     *
     * @return self<T>
     */
    public function orElse(\Closure $fn): self
    {
        return $this->isSome() ? $this : $fn();
    }

    /**
     * Returns {@see Some} if exactly one of `self`, `$option` is {@see Some}, otherwise returns {@see None}.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * $y = Option::Some(3);
     * assert($x->xor($y)->isNone(), 'Expected $x to be None.');
     *
     * $x = Option::None();
     * $y = Option::Some(3);
     * assert($x->xor($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * $y = Option::None();
     * assert($x->xor($y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param self<T> $option The option to compare with
     *
     * @return self<T>
     */
    public function xor(self $option): self
    {
        return $this->isSome() !== $option->isSome() ? $this->or($option) : self::None();
    }

    /**
     * Returns {@see None} if the option is {@see None}, otherwise returns `$option`.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * $y = Option::Some(3);
     * assert($x->and($y)->isSome(), 'Expected $x to be Some.');
     *
     * $x = Option::None();
     * $y = Option::Some(3);
     * assert($x->and($y)->isNone(), 'Expected $x to be None.');
     *
     * $x = Option::None();
     * $y = Option::None();
     * assert($x->and($y)->isNone(), 'Expected $x to be None.');
     * ```
     *
     * @param self<T> $option The option to compare with
     *
     * @return self<T>
     */
    public function and(self $option): self
    {
        return $this->isSome() ? $option : self::None();
    }

    /**
     * Returns {@see None} if the Option is {@see None}, otherwise calls `$fn` with
     * the wrapped value and returns the result.
     *
     * Some languages call this method flatmap.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * $y = $x->andThen(fn($value) => Option::Some($value * 2));
     * assert(4 === $y->unwrap(), 'Expected $y to be 4.');
     *
     * $x = Option::None();
     * $y = $x->andThen(fn($value) => Option::Some($value * 2));
     * assert($y->isNone(), 'Expected $y to be None.');
     * ```
     *
     * @template U
     *
     * @param \Closure(T): self<U> $fn
     *
     * @return self<U>
     */
    public function andThen(\Closure $fn): self
    {
        if ($this->isNone()) {
            return self::None();
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
     * $x = Option::Some(2);
     * assert([2] === iterator_to_array($x->iterate()), 'Expected to be [2].');
     *
     * $x = Option::None();
     * assert([] === iterator_to_array($x->iterate()), 'Expected to be [].');
     * ```
     *
     * @return \ArrayIterator<T>
     */
    public function iterate(): \ArrayIterator
    {
        if ($this->isSome()) {
            return new \ArrayIterator((array) $this->value);
        }

        return new \ArrayIterator();
    }

    /**
     * Returns {@see None} if the Option is {@see None}, otherwise calls `$predicate`
     * with the wrapped value and returns:
     *
     * - {@see Some}(v) If `$predicate` returns `true` (where `v` is the wrapped value), and
     * - {@see None} if `$predicate` returns `false`.
     *
     * <b>Examples</b>
     * ```
     * $isEven = fn ($value) => 0 === $value % 2;
     *
     * assert(Option::None()->filter($isEven)->isSome(), 'Expected to be false.');
     * assert(Option::Some(3)->filter($isEven)->isNone(), 'Expected to be false.');
     * assert(Option::Some(2)->filter($isEven)->isSome(), 'Expected to be true.');
     * ```
     *
     * @param \Closure(T): bool $predicate A closure that returns a boolean
     *
     * @return self<T>
     */
    public function filter(\Closure $predicate): Option
    {
        if ($this->isNone() || $predicate($this->value)) {
            return $this;
        }

        return self::None();
    }

    /**
     * Returns `true` if the `self` value is the same value as `$option`.
     *
     * <b>Examples</b>
     * ```
     * $x = Option::Some(2);
     * $y = Option::Some(2);
     * assert($x->equals($y), 'Expected $x to be equal to $y.');
     *
     * $x = Option::Some(2);
     * $y = Option::Some(3);
     * assert(!$x->equals($y), 'Expected $x not to be equal to $y.');
     *
     * $x = Option::None();
     * $y = Option::None();
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
     * $x = Option::Some(Option::Some(2));
     * assert(2 === $x->flatten()->unwrap(), 'Expected to be 2.');
     *
     * $x = Option::Some(Option::None());
     * assert($x->flatten()->isNone(), 'Expected to be None.');
     *
     * $x = Option::None();
     * assert($x->flatten()->isNone(), 'Expected to be None.');
     *
     * # Flattening only removes one level of nesting at a time:
     * $x = Option::Some(Option::Some(Option::Some(2)));
     * assert(Option::Some(Option::Some(2)) == $x->flatten(), 'Expected to be Option<Option<T>>.');
     * assert(Option::Some(2) == $x->flatten()->flatten(), 'Expected to be Option<T>.');
     * ```
     *
     * @return self<T>
     */
    public function flatten(): self
    {
        if ($this->isNone()) {
            return self::None();
        }

        return $this->value;
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

    private function __construct(
        private mixed $value,
    ) {
    }
}
