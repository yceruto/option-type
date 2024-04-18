# PHP Option type

[![Latest Stable Version](https://poser.pugx.org/yceruto/option-type/v)](https://packagist.org/packages/yceruto/option-type)
[![Unstable](http://poser.pugx.org/yceruto/bundle-skeleton/v/unstable)](https://packagist.org/packages/yceruto/option-type)
[![License](https://poser.pugx.org/yceruto/option-type/license)](https://packagist.org/packages/yceruto/option-type)
[![PHP Version Require](https://poser.pugx.org/yceruto/option-type/require/php)](https://packagist.org/packages/yceruto/option-type)

An Option class that represents an optional value. It's about null safety in PHP!

> [!NOTE]
> Inspired by [Rust's Option type](https://doc.rust-lang.org/std/option/) and other 
> languages like Scala, Swift, F#, etc.

Working with a data type like `Option`, which can explicitly express the absence 
of a value (similar to `None` in other languages), can significantly enhance the 
safety and ease of handling `null` values in PHP! 

It’s a game-changer!

## Installation

```bash
composer require yceruto/option-type
```

## Usage (Handling the presence or absence of a value)

Options are commonly paired with pattern matching to query the presence of a value
and take action, always accounting for the `None` case.

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

// The return value of the function is an Option
$result = divide(10, 2);

// Pattern match to retrieve the value
echo $result->match(
    // The division was valid
    some: fn ($v) => "Result: $v",
    // The division was invalid
    none: fn () => 'Division by zero!',
);
```

> [!TIP]
>Use functions `some()` and `none()` as shortcuts to create an `Option` instance with a
>value, same as `Option::some()`, or without a value, same as `Option::none()`, respectively.

## Documentation

 * [API Reference](docs/api_reference.md)
 * [Examples](docs/examples.md)
 * [Counter-Examples](docs/counter-examples.md)

## License

This software is published under the [MIT License](LICENSE)
