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

namespace Std\Type\Option;

use Std\Type\Option;

if (!\function_exists('some')) {
    /**
     * Some value.
     *
     * @template T
     *
     * @param T $value A value of type T
     *
     * @return Option<T> Some option
     */
    function some(mixed $value): Option
    {
        /** @var Option<T> */
        return Option::some($value);
    }
}

if (!\function_exists('none')) {
    /**
     * No value.
     *
     * @return Option<null> None option
     */
    function none(): Option
    {
        return Option::none();
    }
}
