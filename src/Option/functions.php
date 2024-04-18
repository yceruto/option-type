<?php

namespace Type\Option;

use Type\Option;

if (!\function_exists('some')) {
    /**
     * Some value.
     *
     * @template T
     *
     * @param T $value A value of type T
     *
     * @return Option<T>
     */
    function some(mixed $value): Option
    {
        /** @var Option<T> */
        return Option::Some($value);
    }
}

if (!\function_exists('none')) {
    /**
     * No value.
     *
     * @return Option<null>
     */
    function none(): Option
    {
        return Option::None();
    }
}

