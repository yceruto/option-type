# PHP Option type

[![Latest Stable Version](https://poser.pugx.org/yceruto/option-type/v?v=1)](https://packagist.org/packages/yceruto/option-type)
[![Unstable](http://poser.pugx.org/yceruto/bundle-skeleton/v/unstable)](https://packagist.org/packages/yceruto/option-type)
[![License](https://poser.pugx.org/yceruto/option-type/license)](https://packagist.org/packages/yceruto/option-type)
[![PHP Version Require](https://poser.pugx.org/yceruto/option-type/require/php)](https://packagist.org/packages/yceruto/option-type)

The `Option` class represents a value that might or might not be there—it’s all about 
making sure you handle `null` safely in PHP!

> [!NOTE]
> Inspired by [Rust's Option type](https://doc.rust-lang.org/std/option/) and other 
> languages like Scala, Swift, F#, etc.

Using a data type like `Option`, which clearly shows when there’s no value (similar to 
`None` in other languages), can really boost the safety and simplicity of managing `null` 
values in PHP. It’s a game-changer!

## Installation

```bash
composer require yceruto/option-type
```

## Handling the presence or absence of a value

Options often work with pattern matching to check if there’s a value and act accordingly, 
always making sure to handle the `None` case.

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
>You can use the functions `some()` and `none()` as quick ways to create an `Option` 
>instance. `some()` is just like `Option::some()`, meaning it includes a value, while 
>`none()` is the same as `Option::none()`, indicating it's empty.

## Documentation

 * [API Reference](docs/api_reference.md)
 * [Examples](docs/examples.md)

## License

This software is published under the [MIT License](LICENSE)
