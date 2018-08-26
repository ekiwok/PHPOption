# PHP Option

PHP Option implementations for all scalars. Possible to register custom Option.

For us who prefer strict type checking and don't mind few more opcodes.

```php
   $maybeString   = OptionString::of("test");
   $maybeInt      = OptionInt::of(43);
   $maybeDouble   = OptionDobule::of(0.0);
   $maybeBool     = OptionBoolean::of(false);
   $maybeArray    = OptionArray::of([]);
   $maybeBlogPost = OptionMixed::of($blogPosts->findOneById($uuid));
   
   OptionArray::of(null) instanceof None; // true
   OptionArray::of([])   instanceof Some; // true
```

## Installation

With composer: `composer require ekiwok/option`

## Interface

```php
interface Option<T>
{
    public function equals(Option $another): bool;

    public function isPresent(): bool;

    public function map(callable $mapper, string $typeToWrap = null): Option;

    public function get(): T;
    
    public function orElse(T $value): T;

    public function orElseGet(callable $supplier): T;

    public function orElseThrow(callable $supplier): T;
    
    static public function of(T $value): Option<T>
}
    
```

All scalar options enforce that methods `get`, `orElse`, `orElseGet`, `orElseThrow` returns the same scalar.

So it's not possible to `orElse` float from OptionString:

```php
   return OptionString::of("test")
       ->orElse(34.5);
   // Fatal error: Uncaught TypeError: Argument 1 passed to class@anonymous::orElse() must be of the type string, float given
``` 

The only exception is `OptionMixed` which does not enforce types.

```php
   return OptionMixed::of("test")
        ->orElse(34.5);
```

**Important thing to notice** is that when you map Option which is None it will return OptionMixed.

```php
    $maybeIsPalindrome = OptionString::of(null)
        ->map('isPalindrome');
    
    $maybeIsPalindrome instanceof OptionBoolean; // false
    $maybeIsPalindrome instanceof OptionMixed;   // true
```

This is because there is no reasonable way to guess what should be the type of the value returned by a $supplier. In Some we are able to wrap accordingly to the type of returned value.

On top of that you probably do not care about mapping when you're dealing with None because all further mappings will also return None.

But if you really want to ensure that, for example, `orElseGet` $supplier returns value of correct type you might provide expected type as another map parameter:

```php
   $maybeIsPalindrome = OptionString::of(null)
       ->map('isPalindrome', 'boolean');
   
   $maybeIsPalindrome instanceof OptionBoolean; // true
   $maybeIsPalindrome instanceof OptionMixed;   // false
   
   $maybeIsPalindrome->orElseGet(function () {
      return null;
   });
   // Fatal error: Uncaught TypeError: Return value of class@anonymous::orElseGet() must be of the type boolean
```

