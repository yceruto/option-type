# Option type examples

This section provides examples of how to use the `Option` type in your PHP code.

## Example 1: Handling the presence or absence of a value

The following example demonstrates how to use the `Option` type to handle the presence or absence of a value safely.

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

$result = divide(10, 2)->expect('10 divided by 2.');
```
