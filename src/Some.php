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

/**
 * Some value.
 *
 * Also see {@see some()} for a shorter way to create a Some Option.
 *
 * @template T
 *
 * @implements Option<T>
 */
final readonly class Some implements Option
{
    private mixed $value;

    /**
     * @param T $value A value of type T
     */
    public function __construct(mixed $value)
    {
        if (null === $value) {
            throw new LogicOptionException(sprintf('Cannot create %s option with a null value, use %s instead.', self::class, None::class));
        }

        $this->value = $value;
    }

    public function isSome(): bool
    {
        return true;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function match(callable $some, callable $none): mixed
    {
        return $some($this->value);
    }

    public function expect(string $message): mixed
    {
        return $this->value;
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $fn): mixed
    {
        return $this->value;
    }

    public function unwrapOrThrow(\Throwable $error): mixed
    {
        return $this->value;
    }

    public function map(callable $fn): Option
    {
        if (null === $value = $fn($this->value)) {
            return new None();
        }

        return new self($value);
    }

    public function mapOr(callable $fn, mixed $default): mixed
    {
        return $fn($this->value);
    }

    public function mapOrElse(callable $fn, callable $default): mixed
    {
        return $fn($this->value);
    }

    /**
     * @return Some<T>
     */
    public function or(Option $option): self
    {
        return $this;
    }

    /**
     * @return Some<T>
     */
    public function orElse(callable $fn): self
    {
        return $this;
    }

    public function xor(Option $option): Option
    {
        return $option instanceof self ? new None() : $this;
    }

    public function and(Option $option): Option
    {
        return $option;
    }

    public function andThen(callable $fn): Option
    {
        return $fn($this->value);
    }

    public function iterate(): iterable
    {
        return new \ArrayIterator((array) $this->value);
    }

    /**
     * @return None|Some<T>
     */
    public function filter(callable $predicate): Option
    {
        if ($predicate($this->value)) {
            return $this;
        }

        return new None();
    }

    public function equals(Option $option): bool
    {
        return $option instanceof self && $this->value === $option->value;
    }

    public function flatten(): Option
    {
        if ($this->value instanceof Option) {
            return $this->value;
        }

        throw new LogicOptionException(sprintf('Calling %s() method on a non-Option value. Unexpected "%s" type.', __METHOD__, get_debug_type($this->value)));
    }

    /**
     * @return Some<T>
     */
    public function clone(): self
    {
        return clone $this;
    }
}
