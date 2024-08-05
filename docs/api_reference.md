# PHP `Option` Type Documentation

The `Option` type in PHP is designed to handle the presence or absence of a value explicitly as part of its state.
This allows developers to write more robust code by avoiding common errors like type errors and undefined indices.

_Table of Contents:_

- Creating an Option
  - [some](#optionsomemixed-value-self)
  - [none](#optionnone-self)
  - [from](#optionfrommixed-value-self)
  - [clone](#optionclone-self)
- Querying the variant
  - [isSome](#optionissome-bool)
  - [isNone](#optionisnone-bool)
- Extracting the contained value
  - [expect](#optionexpectstring-message-mixed)
  - [unwrap](#optionunwrap-mixed)
  - [unwrapOr](#optionunwrapormixed-default-mixed)
  - [unwrapOrElse](#optionunwraporelsecallable-fn-mixed)
  - [unwrapOrThrow](#optionunwraporthrowthrowable-error-mixed)
  - [flatten](#optionflatten-self)
- Transforming contained values
  - [match](#optionmatchcallable-some-callable-none-mixed)
  - [map](#optionmapcallable-fn-self)
  - [mapOr](#optionmaporcallable-fn-mixed-default-mixed)
  - [mapOrElse](#optionmaporelsecallable-fn-callable-default-mixed)
  - [andThen](#optionandthencallable-fn-self)
  - [filter](#optionfiltercallable-predicate-self)
- Boolean operators
  - [or](#optionorself-option-self)
  - [orElse](#optionorelsecallable-fn-self)
  - [xor](#optionxorself-option-self)
  - [and](#optionandself-option-self)
- Iterating over `Option`
  - [iterate](#optioniterate-arrayiterator)
- Comparison operators
  - [equals](#optionequalsself-option-bool)
- Shortcut functions
  - [some](#somemixed-value-option)
  - [none](#none-option)

## Creating an `Option`

### **`Some(mixed $value)`**

Creates an `Option` instance containing a non-null value. If `null` is passed, a `LogicOptionException` is thrown.

```php
$opt = new Some(10);
echo $opt->unwrap(); // Outputs: 10

new Some(null); // Throws LogicOptionException
```

### **`None()`**

Creates an `Option` instance representing the absence of a value (i.e., `None`).

```php
$opt = new None();
echo $opt->isNone(); // Outputs: true
```

### **`OptionFactory::from(mixed $value): Option`**

Converts a value to an `Option`, making it `None` if the original value is `null`, otherwise `Some`.

```php
$some = OptionFactory::from(5);
$none = OptionFactory::from(null);
echo $some->isSome(); // Outputs: true
echo $none->isNone(); // Outputs: true
```

### **`Option::clone(): self`**

Creates a copy of the option. Useful for preserving immutability when needed.

```php
$x = new Some(2);
$y = $x->clone();
echo $y->unwrap(); // Outputs: 2
```

## Querying the variant

### **`Option::isSome(): bool`**

Checks if the option is a `Some` value.

```php
$opt = new Some(10);
echo $opt->isSome(); // Outputs: true
```

### **`Option::isNone(): bool`**

Checks if the option is a `None` value.

```php
$opt = new None();
echo $opt->isNone(); // Outputs: true
```

## Extracting the contained value

### **`Option::expect(string $message): mixed`**

Returns the contained value if it is `Some`; otherwise, throws a `RuntimeOptionException` with a custom message.

```php
$opt = new Some(10);
echo $opt->expect('A number.'); // Outputs: 10

$opt = new None();
echo $opt->expect('A number.'); // Throws RuntimeOptionException with custom message
```

### **`Option::unwrap(): mixed`**

Returns the contained value if it is `Some`; otherwise, throws a `RuntimeOptionException`.

```php
$opt = new Some(10);
echo $opt->unwrap(); // Outputs: 10

$opt = new None();
echo $opt->unwrap(); // Throws RuntimeOptionException
```

### **`Option::unwrapOr(mixed $default): mixed`**

Returns the contained value if it is `Some`; otherwise, returns a provided default value.

```php
$opt = new Some(10);
echo $opt->unwrapOr(5); // Outputs: 10

$opt = new None();
echo $opt->unwrapOr(5); // Outputs: 5
```

### **`Option::unwrapOrElse(callable $fn): mixed`**

Returns the contained value if it is `Some`; otherwise, computes a value using a provided callable.

```php
$opt = new Some(10);
echo $opt->unwrapOrElse(fn () => 5); // Outputs: 10

$opt = new None();
echo $opt->unwrapOrElse(fn () => 5); // Outputs: 5
```

### **`Option::unwrapOrThrow(\Throwable $error): mixed`**

Returns the contained value if it is `Some`; otherwise, throws the provided throwable error.

```php
$opt = new Some(10);
echo $opt->unwrapOrThrow(new \Exception("No value!")); // Outputs: 10

$opt = new None();
echo $opt->unwrapOrThrow(new \Exception("No value!")); // Throws Exception
```

### **`Option::flatten(): self`**

Converts from `Option<Option<T>>` to `Option<T>`. Useful for unwrapping nested options.

```php
$x = new Some(new Some(2));
echo $x->flatten()->unwrap(); // Outputs: 2

$x = new Some(new None());
echo $x->flatten()->isNone(); // Outputs: true
```

## Transforming contained values

### **`Option::match(callable $some, callable $none): mixed`**

Applies a function to the contained value if it is `Some`, otherwise applies another function.

```php
$x = new Some(2);
echo $x->match(
    some: fn ($v) => $v * 2, 
    none: fn () => 0,
); // Outputs: 4

$x = new None();
echo $x->match(
    some: fn ($v) => $v * 2, 
    none: fn () => 0,
); // Outputs: 0
```

### **`Option::map(callable $fn): self`**

Transforms the contained value by applying a function if it is `Some`; returns `None` if it is `None`.

```php
$opt = new Some(5);
$result = $opt->map(fn ($x) => $x * 2);
echo $result->unwrap(); // Outputs: 10

$opt = new None();
$result = $opt->map(fn ($x) => $x * 2);
echo $result->isNone(); // Outputs: true
```

### **`Option::mapOr(callable $fn, mixed $default): mixed`**

Applies a function to the contained value if it is `Some`, otherwise returns a default value.

```php
$opt = new Some(5);
echo $opt->mapOr(fn ($x) => $x * 2, 0); // Outputs: 10

$opt = new None();
echo $opt->mapOr(fn ($x) => $x * 2, 0); // Outputs: 0
```

### **`Option::mapOrElse(callable $fn, callable $default): mixed`**

Applies a function to the contained value if it is `Some`, otherwise computes the default using another callable.

```php
$opt = new Some(5);
echo $opt->mapOrElse(fn ($x) => $x * 2, fn () => 0); // Outputs: 10

$opt = new None();
echo $opt->mapOrElse(fn ($x) => $x * 2, fn () => 0); // Outputs: 0
```

### **`Option::andThen(callable $fn): self`**

Applies a function to the contained value if it is `Some`, otherwise returns `None`. This is also known as flatmap
in other languages.

```php
$x = new Some(2);
$result = $x->andThen(fn ($value) => new Some($value * 2));
echo $result->unwrap(); // Outputs: 4

$x = new None();
$result = $x->andThen(fn ($value) => new Some($value * 2));
echo $result->isNone(); // Outputs: true
```

### **`Option::filter(callable $predicate): self`**

Returns `Some` if the option is `Some` and the predicate returns `true`; otherwise, returns `None`.

```php
$isEven = fn ($x) => $x % 2 === 0;

$x = new Some(4);
echo $x->filter($isEven)->isSome(); // Outputs: true

$x = new Some(5);
echo $x->filter($isEven)->isNone(); // Outputs: true
```

## Boolean operators

### **`Option::or(self $option): self`**

Returns the option itself if it is `Some`, otherwise returns the provided option.

```php
$x = new Some(2);
$y = new Some(3);
echo $x->or($y)->unwrap(); // Outputs: 2

$x = new None();
echo $x->or($y)->unwrap(); // Outputs: 3
```

### **`Option::orElse(callable $fn): self`**

Returns the option itself if it is `Some`, otherwise calls a callable to provide an option.

```php
$x = new Some(2);
$y = new Some(3);
echo $x->orElse(fn () => $y)->unwrap(); // Outputs: 2

$x = new None();
echo $x->orElse(fn () => $y)->unwrap(); // Outputs: 3
```

### **`Option::xor(self $option): self`**

Returns `Some` if exactly one of the options is `Some`, otherwise returns `None`.

```php
$x = new Some(2);
$y = new Some(3);
echo $x->xor($y)->isNone(); // Outputs: true

$x = new None();
echo $x->xor($y)->unwrap(); // Outputs: 3
```

### **`Option::and(self $option): self`**

Returns the provided option if the original option is `Some`, otherwise returns `None`.

```php
$x = new Some(2);
$y = new Some(3);
echo $x->and($y)->unwrap(); // Outputs: 3

$x = new None();
echo $x->and($y)->isNone(); // Outputs: true
```

## Iterating over `Option`

### **`Option::iterate(): \ArrayIterator`**

Provides an iterator over the contained value if it is `Some`, otherwise an empty iterator.

```php
$x = new Some(2);
foreach ($x->iterate() as $v) {
    echo $v; // Outputs: 2
}

$x = new None();
foreach ($x->iterate() as $v) {
    // 0 iterations
}
```

## Comparison operators

### **`Option::equals(self $option): bool`**

Compares two options for equality. Returns `true` if both are `None` or if both are `Some` with equal values.

```php
$x = new Some(2);
$y = new Some(2);
echo $x->equals($y); // Outputs: true

$x = new None();
$y = new None();
echo $x->equals($y); // Outputs: true
```

## Shortcut Functions

### **`some(mixed $value): Option`**

Creates an `Option` instance containing a non-null value. If `null` is passed, a `LogicOptionException` is thrown.

```php
$opt = some(10); // Same as new Some(10)
echo $opt->unwrap(); // Outputs: 10

some(null); // Throws LogicOptionException
```

### **`none(): Option`**

Creates an `Option` instance representing the absence of a value (i.e., `None`).

```php
$opt = none(); // Same as new None()
echo $opt->isNone(); // Outputs: true
```

See the [examples](examples.md) for more details on how to apply effectively the `Option` type in your PHP code.
