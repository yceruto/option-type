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

use Std\Type\Exception\RuntimeOptionException;

/**
 * No value.
 *
 * Also see {@see none()} for a shorter way to create a None Option.
 *
 * @implements Option<null>
 */
final readonly class None implements Option
{
    public function isSome(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function match(callable $some, callable $none): mixed
    {
        return $none();
    }

    public function expect(string $message): mixed
    {
        throw new RuntimeOptionException($message);
    }

    public function unwrap(): mixed
    {
        throw new RuntimeOptionException(sprintf('Calling unwrap() method on a %s option. Check isNone() method first or use a fallback method instead.', self::class));
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(callable $fn): mixed
    {
        return $fn();
    }

    public function unwrapOrThrow(\Throwable $error): mixed
    {
        throw $error;
    }

    public function map(callable $fn): self
    {
        return $this;
    }

    public function mapOr(callable $fn, mixed $default): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $fn, callable $default): mixed
    {
        return $default();
    }

    public function or(Option $option): Option
    {
        return $option;
    }

    public function orElse(callable $fn): Option
    {
        return $fn();
    }

    public function xor(Option $option): Option
    {
        return $option instanceof self ? $this : $option;
    }

    public function and(Option $option): Option
    {
        return $this;
    }

    public function andThen(callable $fn): self
    {
        return $this;
    }

    public function iterate(): iterable
    {
        return [];
    }

    public function filter(callable $predicate): self
    {
        return $this;
    }

    public function equals(Option $option): bool
    {
        return $option instanceof self;
    }

    public function flatten(): self
    {
        return $this;
    }

    public function clone(): self
    {
        return clone $this;
    }
}
