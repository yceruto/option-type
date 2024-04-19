# Option type examples

This section provides examples of how to use the `Option` type in your PHP code.

## Example 1: Handling the presence or absence of a value in return types

Let's say you have a function that retrieves a user from a database by its ID. The 
function can return `null` if the user is not found. Instead of returning `null`, 
you can use the `Option` type to explicitly handle the presence or absence of the user.

```php
use App\Model\User;
use Std\Type\Option;

/**
 * @return Option<User>
 */
function findUser(int $id): Option
{
    $user = // get user from database by $id ... it can return null

    return Option::from($user); // None if null, otherwise Some($user)
}

$user = findUser(1)->expect('the user exists.'); // throws LogicException if it does not exist
// do something safely with $user instance...

// map the user found to a DTO or create a new one if not found
$dto = findUser(1)->mapOr(UserDto::from(...), UserDto::new());
// do something safely with $dto instance...
```
