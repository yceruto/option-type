## PHP `Option` Type Documentation

The `Option` type in PHP is designed to handle the presence or absence of a value explicitly as part of its state. 
This allows developers to write more robust code by avoiding common errors like type errors and undefined indices.

### **`some(mixed $value): self`**

Creates an `Option` instance containing a non-null value. If `null` is passed, a `LogicException` is thrown.

```php
$opt = Option::some(10);
echo $opt->unwrap(); // Outputs: 10

Option::some(null); // Throws LogicException
```

### **`none(): self`**

Creates an `Option` instance representing the absence of a value (i.e., `None`).

```php
$opt = Option::none();
echo $opt->isNone(); // Outputs: true
```

### **`from(mixed $value): self`**

Converts a value to an `Option`, making it `None` if the original value is `null`, otherwise `Some`.

```php
$some = Option::from(5);
$none = Option::from(null);
echo $some->isSome(); // Outputs: true
echo $none->isNone(); // Outputs: true
```

### **`isSome(): bool`**

Checks if the option is a `Some` value.

```php
$opt = Option::some(10);
echo $opt->isSome(); // Outputs: true
```

### **`isNone(): bool`**

Checks if the option is a `None` value.

```php
$opt = Option::none();
echo $opt->isNone(); // Outputs: true
```

### **`expect(string $message): mixed`**

Returns the contained value if it is `Some`; otherwise, throws a `LogicException` with a custom message.

```php
$opt = Option::some(10);
echo $opt->expect('A number.'); // Outputs: 10

$opt = Option::none();
echo $opt->expect('A number.'); // Throws LogicException
```

### **`unwrap(): mixed`**

Returns the contained value if it is `Some`; otherwise, throws a `LogicException`.

```php
$opt = Option::some(10);
echo $opt->unwrap(); // Outputs: 10

$opt = Option::none();
echo $opt->unwrap(); // Throws LogicException
```

### **`unwrapOr(mixed $default): mixed`**

Returns the contained value if it is `Some`; otherwise, returns a provided default value.

```php
$opt = Option::some(10);
echo $opt->unwrapOr(5); // Outputs: 10

$opt = Option::none();
echo $opt->unwrapOr(5); // Outputs: 5
```

### **`unwrapOrElse(callable $fn): mixed`**

Returns the contained value if it is `Some`; otherwise, computes a value using a provided callable.

```php
$opt = Option::some(10);
echo $opt->unwrapOrElse(fn () => 5); // Outputs: 10

$opt = Option::none();
echo $opt->unwrapOrElse(fn () => 5); // Outputs: 5
```

### **`unwrapOrThrow(\Throwable $error): mixed`**

Returns the contained value if it is `Some`; otherwise, throws the provided throwable error.

```php
$opt = Option::some(10);
echo $opt->unwrapOrThrow(new \Exception("No value!")); // Outputs: 10

$opt = Option::none();
echo $opt->unwrapOrThrow(new \Exception("No value!")); // Throws Exception
```

### **`map(callable $fn): self`**

Transforms the contained value by applying a function if it is `Some`; returns `None` if it is `None`.

```php
$opt = Option::some(5);
$result = $opt->map(fn ($x) => $x * 2);
echo $result->unwrap(); // Outputs: 10

$opt = Option::none();
$result = $opt->map(fn ($x) => $x * 2);
echo $result->isNone(); // Outputs: true
```

### **`mapOr(callable $fn, mixed $default): mixed`**

Applies a function to the contained value if it is `Some`, otherwise returns a default value.

```php
$opt = Option::some(5);
echo $opt->mapOr(fn ($x) => $x * 2, 0); // Outputs: 10

$opt = Option::none();
echo $opt->mapOr(fn ($x) => $x * 2, 0); // Outputs: 0
```

### **`mapOrElse(callable $fn, callable $default): mixed`**

Applies a function to the contained value if it is `Some`, otherwise computes the default using another callable.

```php
$opt = Option::some(5);
echo $opt->mapOrElse(fn ($x) => $x * 2, fn () => 0); // Outputs: 10

$opt = Option::none();
echo $opt->mapOrElse(fn ($x) => $x * 2, fn () => 0); // Outputs: 0
```

### **`or(self $option): self`**

Returns the option itself if it is `Some`, otherwise returns the provided option.

```php
$x = Option::some(2);
$y = Option::some(3);
echo $x->or($y)->unwrap(); // Outputs: 2

$x = Option::none();
echo $x->or($y)->unwrap(); // Outputs: 3
```

### **`orElse(callable $fn): self`**

Returns the option itself if it is `Some`, otherwise calls a callable to provide an option.

```php
$x = Option::some(2);
$y = Option::some(3);
echo $x->orElse(fn () => $y)->unwrap(); // Outputs: 2

$x = Option::none();
echo $x->orElse(fn () => $y)->unwrap(); // Outputs: 3
```

### **`xor(self $option): self`**

Returns `Some` if exactly one of the options is `Some`, otherwise returns `None`.

```php
$x = Option::some(2);
$y = Option::some(3);
echo $x->xor($y)->isNone(); // Outputs: true

$x = Option::none();
echo $x->xor($y)->unwrap(); // Outputs: 3
```

### **`and(self $option): self`**

Returns the provided option if the original option is `Some`, otherwise returns `None`.

```php
$x = Option::some(2);
$y = Option::some(3);
echo $x->and($y)->unwrap(); // Outputs: 3

$x = Option::none();
echo $x->and($y)->isNone(); // Outputs: true
```

### **`andThen(callable $fn): self`**

Applies a function to the contained value if it is `Some`, otherwise returns `None`. This is also known as flatmap 
in other languages.

```php
$x = Option::some(2);
$result = $x->andThen(fn ($value) => Option::some($value * 2));
echo $result->unwrap(); // Outputs: 4

$x = Option::none();
$result = $x->andThen(fn ($value) => Option::some($value * 2));
echo $result->isNone(); // Outputs: true
```

### **`iterate(): \ArrayIterator`**

Provides an iterator over the contained value if it is `Some`, otherwise an empty iterator.

```php
$x = Option::some(2);
$iterator = $x->iterate();
echo iterator_to_array($iterator)[0]; // Outputs: 2

$x = Option::none();
$iterator = $x->iterate();
echo count(iterator_to_array($iterator)); // Outputs: 0
```

### **`filter(callable $predicate): self`**

Returns `Some` if the option is `Some` and the predicate returns `true`; otherwise, returns `None`.

```php
$isEven = fn ($x) => $x % 2 === 0;
$x = Option::some(4);
$filtered = $x->filter($isEven);
echo $filtered->isSome(); // Outputs: true

$x = Option::some(5);
$filtered = $x->filter($isEven);
echo $filtered->isNone(); // Outputs: true
```

### **`equals(self $option): bool`**

Compares two options for equality. Returns `true` if both are `None` or if both are `Some` with equal values.

```php
$x = Option::some(2);
$y = Option::some(2);
echo $x->equals($y); // Outputs: true

$x = Option::none();
$y = Option::none();
echo $x->equals($y); // Outputs: true
```

### **`flatten(): self`**

Converts from `Option<Option<T>>` to `Option<T>`. Useful for unwrapping nested options

.

```php
$x = Option::some(Option::some(2));
echo $x->flatten()->unwrap(); // Outputs: 2

$x = Option::some(Option::none());
echo $x->flatten()->isNone(); // Outputs: true
```

### **`clone(): self`**

Creates a copy of the option. Useful for preserving immutability when needed.

```php
$x = Option::some(2);
$y = $x->clone();
echo $y->unwrap(); // Outputs: 2
```

This documentation provides a comprehensive guide to using the `Option` class effectively for handling optional values in PHP, which can significantly reduce bugs related to null values in your applications.
