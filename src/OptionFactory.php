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

final readonly class OptionFactory
{
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
     * @template T of mixed
     *
     * @param T $value A value of type T
     *
     * @return None|Some<T> Some or None Option
     */
    public static function from(mixed $value): None|Some
    {
        return null === $value ? new None() : new Some($value);
    }

    private function __construct()
    {
    }
}
