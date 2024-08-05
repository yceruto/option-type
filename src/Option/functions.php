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

namespace Std\Type\Option;

use Std\Type\None;
use Std\Type\Some;

if (!\function_exists('some')) {
    /**
     * Some value.
     *
     * @template T
     *
     * @param T $value A value of type T
     *
     * @return Some<T> Some option
     */
    function some(mixed $value): Some
    {
        return new Some($value);
    }
}

if (!\function_exists('none')) {
    /**
     * No value.
     */
    function none(): None
    {
        return new None();
    }
}
