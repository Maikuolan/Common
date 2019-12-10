### Documentation for the "ComplexStringHandler" class.

*The complex string handler class provides an easy way to iterate over the parts of a given string, identified by a given pattern, in order to execute a given closure to those parts of the given string, or to the glue that separates those parts.*

---


### How to use:

- [ComplexStringHandler constructor.](#complexstringhandler-constructor)
- [generateMarkers method.](#generatemarkers-method)
- [iterateClosure method.](#iterateclosure-method)
- [recompile method.](#recompile-method)
- [__toString magic method.](#__tostring-magic-method)

#### ComplexStringHandler constructor.

```PHP
public function __construct(string $Data = '', string $Pattern = '', callable $Closure = null);
```

The first parameter, `$Data`, should be a string to be worked with. The second parameter, `$Pattern`, should be a pattern, expressed as a PCRE regular expression, to be used to identify potential delimiters (or glue) within the supplied string in order to be able to iterate over substrings of the strings (per the potential delimiters), during which those substrings should be processed by the supplied closure. The third parameter, `$Closure`, should be a closure, and will be used when iterating over the aforementioned substrings.

All parameters are optional. The constructor allows this information to be supplied to the object during instantiation, but it can also be supplied to the object after instantiation, too. An instance of ComplexStringHandler is effectively reusable, capable of performing multiple, unrelated operations to multiple, unrelated strings (though instantiation of a new object for subsequent operations may perhaps result in cleaner, more maintainable code in many cases).

A simple example, to demonstrate two different ways to achieve the same results:

```PHP
<?php
// The string to work with.
$TheString = 'ab0cd1ef2gh3ij4kl5mn6op7qr8st9uv';
// The pattern to use.
$ThePattern = '~(\D+)~';
echo 'The string to work with: ' . $TheString . PHP_EOL;

// The first way (information supplied during instantiation).
$ObjectA = new \Maikuolan\Common\ComplexStringHandler($TheString, $ThePattern, function ($Data) {
    return $Data === '' ? '' : ' "' . (((int)$Data + 1)) . '" ';
});
$ObjectA->iterateClosure(function ($Data) {
    return '(' . $Data . ')';
}, true);
echo 'The first way: ' . $ObjectA->recompile() . PHP_EOL;

// The second way (information supplied after instantiation).
$ObjectB = new \Maikuolan\Common\ComplexStringHandler();
$ObjectB->Input = $TheString;
$ObjectB->generateMarkers($ThePattern);
$ObjectB->iterateClosure(function ($Data) {
    return $Data === '' ? '' : ' "' . (((int)$Data + 1)) . '" ';
}, false);
$ObjectB->iterateClosure(function ($Data) {
    return '(' . $Data . ')';
}, true);
echo 'The second way: ' . $ObjectB->recompile() . PHP_EOL;
```

Output:

```
The string to work with: ab0cd1ef2gh3ij4kl5mn6op7qr8st9uv
The first way: (ab) "1" (cd) "2" (ef) "3" (gh) "4" (ij) "5" (kl) "6" (mn) "7" (op) "8" (qr) "9" (st) "10" (uv)
The second way: (ab) "1" (cd) "2" (ef) "3" (gh) "4" (ij) "5" (kl) "6" (mn) "7" (op) "8" (qr) "9" (st) "10" (uv)
```

#### generateMarkers method.

The generateMarkers method identifies potential delimiters (or glue) within the previously supplied string, using a pattern supplied as the method's sole parameter.

```PHP
public function generateMarkers(string $Pattern);
```

#### iterateClosure method.

The iterateClosure method, depending on whether its second parameter is true or false, iterates over either the substrings, or the delimiters/glue, as per previously identified by generateMarkers, of the previously supplied string, and during iteration, uses the callable/closure supplied as the method's first parameter to process those substrings or delimiters/glue.

```PHP
public function iterateClosure(callable $Closure, bool $Glue = false);
```

#### recompile method.

The recompile method returns a string created using the substrings and delimiters/glue of the previously supplied string, generally intended to be called after any necessary processing has been completed. It accepts no parameters.

```PHP
public function recompile(): string;
```

#### __toString magic method.

Attempting to use the object as a string should have the same effect as calling `recompile()`.

```PHP
public function __toString(): string;
```

---


Last Updated: 10 December 2019 (2019.12.10).
