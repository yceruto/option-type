# An Option type for PHP

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

## Usage

Class `Option` represents an optional value: every `Option` is either Some and 
contains a value, or None, and does not.

```php
use App\Model\User;
use Std\Type\Option;

use function Std\Type\Option\none;
use function Std\Type\Option\some;

/**
 * @return Option<User>
 */
function findUser(int $id): Option
{
    $user = // get user from database by $id ... it can return null
    
    if (null === $user) {
        return none();
    }

    return some($user);
}

// basic usage
$user = findUser(1)->expect('A user must be found.');

// do something with $user safely

// advanced usage (map the user to a DTO)
$dto = findUser(1)->mapOr(UserDto::from(...), UserDto::new());

// do something with $dto safely
```

> [!TIP]
>Use functions `some()` and `none()` as shortcuts to create an `Option` instance with a
>value, same as `Option::some()`, or without a value, same as `Option::none()`, respectively.

## Documentation

[TODO]

## License

This software is published under the [MIT License](LICENSE)
