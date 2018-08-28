# PHP Option

Option is a value that might or might not be present. In other words it's elegant alternative to accepting null.

Allow null approach:

```php
    $uuid = $request->get('id');
    
    if ($uuid === null) {
       return new NotFoundResponse();
    }
    
    $product = $this->products->findOneBy($uuid);
    
    if ($product === null) {
       return new NotFoundResponse();
    }
    
    return new JsonResponse($product);
```

Options approach:

```php
    return Optional::of($request->get('id'))
        ->map(function (string $uuid) {
            return $this->products->findOneBy($uuid);
        })
        ->map(function (Product $product) {
            return new JsonResponse($product);
        })
        ->orElse(new NotFoundResponse());
```

In contrary to other libraries this one implements spearate Option for each scalar type and allows registering custom Options for objects. It's the closest to Java templates we can get and enforces strict type checking.

So if you prefer strict type checking over having a few opcodes less, you can enforce:

```php
    return Optional::Some("I love strict types")->orElse(new \stdClass());
    // TypeError: Argument 1 passed to class@anonymous::orElse() must be of the type string, object given, called in ...

```  

`Optional::Some` and `Optional::of` wraps each value to correct Option<T> class:

```php
   $maybeString   = Optional::of("test");                          // OptionString
   $maybeInt      = Optional::of(43);                              // OptionInteger
   $maybeDouble   = Optional::of(0.0);                             // OptionDobule
   $maybeBool     = Optional::of(false);                           // OptionBoolean
   $maybeArray    = Optional::of([]);                              // OpionArray
   $maybeBlogPost = Optional::of($blogPosts->findOneById($uuid));  // Optional
   
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
    
    static public function Some($value): Some
    
    static public function None(): None
}
    
```

All scalar options enforce that methods `get`, `orElse`, `orElseGet`, `orElseThrow` returns the same scalar.

So it's not possible to `orElse` float from OptionString:

```php
   return OptionString::of("test")
       ->orElse(34.5);
   // Fatal error: Uncaught TypeError: Argument 1 passed to class@anonymous::orElse() must be of the type string, float given
``` 

The only exception is `OptionAny` which does not enforce types.

```php
   return OptionAny::of("test")
        ->orElse(34.5);
```

**Important thing to notice** is that when you map Option which is None it will return OptionAny.

```php
    $maybeIsPalindrome = OptionString::of(null)
        ->map('isPalindrome');
    
    $maybeIsPalindrome instanceof OptionBoolean; // false
    $maybeIsPalindrome instanceof OptionAny;   // true
```

This is because there is no reasonable way to guess what should be the type of the value returned by a $supplier. In Some we are able to wrap accordingly to the type of returned value.

On top of that you probably do not care about mapping when you're dealing with None because all further mappings will also return None.

But if you really want to ensure that, for example, `orElseGet` $supplier returns value of correct type you might provide expected type as another map parameter:

```php
   $maybeIsPalindrome = OptionString::of(null)
       ->map('isPalindrome', 'boolean');
   
   $maybeIsPalindrome instanceof OptionBoolean; // true
   $maybeIsPalindrome instanceof OptionAny;   // false
   
   $maybeIsPalindrome->orElseGet(function () {
      return null;
   });
   // Fatal error: Uncaught TypeError: Return value of class@anonymous::orElseGet() must be of the type boolean
```

