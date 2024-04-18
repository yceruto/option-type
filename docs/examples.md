# Option type examples

This section provides examples of how to use the `Option` type in your PHP code.

## Example 1: Handling the presence or absence of a value

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
$user = findUser(1)->expect('user exists.');
// do something safely with $user instance...

// advanced usage (map the user to a DTO)
$dto = findUser(1)->mapOr(UserDto::from(...), UserDto::new());
// do something safely with $dto instance...
```
