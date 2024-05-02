# PHP Option type

[![Latest Stable Version](https://poser.pugx.org/yceruto/option-type/v?v=1)](https://packagist.org/packages/yceruto/option-type)
[![Unstable](http://poser.pugx.org/yceruto/bundle-skeleton/v/unstable)](https://packagist.org/packages/yceruto/option-type)
[![License](https://poser.pugx.org/yceruto/option-type/license)](https://packagist.org/packages/yceruto/option-type)
[![PHP Version Require](https://poser.pugx.org/yceruto/option-type/require/php)](https://packagist.org/packages/yceruto/option-type)

The `Option` type represents a value that might or might not be there. It's all about
null safety in PHP!

> [!NOTE]
> Inspired by [Rust's Option type](https://doc.rust-lang.org/std/option/) and other 
> languages like Scala, Swift, F#, etc.

## Installation

```bash
composer require yceruto/option-type
```

## Handling the presence or absence of a value with `null`

In PHP, denoting the absence of a value is done with `null`, e.g. when a `divide`
function returns `null` if the divisor is `0`.

```php
function divide(int $dividend, int $divisor): ?int
{
    if (0 === $divisor) {
        return null;
    }

    return intdiv($dividend, $divisor);
}

function success(int $result): string {
    return sprintf('Result: %d', $result);
}

$result = divide(10, 2);

echo success($result);
```

Can you spot the issue in this code? Apparently, everything is fine until you try to
divide by zero. The function will return `null`, and the `success()` function will throw
a `TypeError` because it expects an `int` value, not `null`.

The issue with this approach is that it's too easy to overlook checking if the value is 
`null`, leading to runtime errors, and this is where the `Option` type comes in handy: it 
always forces you to deal with the `null` case.

## Handling the presence or absence of a value with `Option`

Options often work with pattern matching to check if there’s a value and act accordingly, 
always making sure to handle the `null` case.

```php
use Std\Type\Option;
use function Std\Type\Option\none;
use function Std\Type\Option\some;

/**
 * @return Option<int>
 */
function divide(int $dividend, int $divisor): Option
{
    if (0 === $divisor) {
        return none();
    }

    return some(intdiv($dividend, $divisor));
}

function success(int $result): string {
    return sprintf('Result: %d', $result);
}

// The return value of the function is an Option
$result = divide(10, 2);

// Pattern match to retrieve the value
echo $result->match(
    // The division was valid
    some: fn (int $v) => success($v),
    // The division was invalid
    none: fn () => 'Division by zero!',
);
```

> [!TIP]
>You can use the functions `some()` and `none()` as quick ways to create an `Option` 
>instance. `some()` is just like `Option::some()`, meaning it includes a value, while 
>`none()` is the same as `Option::none()`, indicating it's empty.

## Documentation

 * [API Reference](docs/api_reference.md)
 * [Examples](docs/examples.md)

## License

This software is published under the [MIT License](LICENSE)
