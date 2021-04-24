### Documentation for the "Operation" class.

*Used by CIDRAM and phpMussel for various operations related to dependency management (an integral part of the internal updates system).*

---


### How to use:

- [Cache member.](#cache-member)
- [opVersions method.](#opversions-method)
- [opEqualTo method.](#opequalto-method)
- [multiCompare method.](#multicompare-method)
- [singleCompare method.](#singlecompare-method)
- [splitVersionParts method.](#splitversionparts-method)
- [dataTraverse method.](#datatraverse-method)
- [ifCompare method.](#ifcompare-method)

#### Cache member.

This private member is an array, where the results returned by `singleCompare` are cached. When `singleCompare` executes, it'll first check whether an identical execution has already occurred for the instance, returning the cached results instead of calculating new results. This may marginally improve performance in cases where the same compare operation occurs multiple times for the instance.

```PHP
private $Cache = [];
```

#### opVersions method.

This method is public in case the need to call it directly arises, but you should generally just call `singleCompare` or `multiCompare` instead.

After `singleCompare` has determined which kind of operation is needed, `opVersions` and `opEqualTo` are used internally to perform the operation in question. In particular, `opVersions` is responsible for handling any operations involving *greater* or *less than* comparisons.

```PHP
public function opVersions(string $Actual, string $Constraint, bool $NextMajor, bool $GreaterThan, bool $OrEqualTo): bool
```

`opVersions` accepts 5 parameters. The first parameter is a string, the actual value being compared. The second parameter is a string, the constraint the actual value being compared must match against. The third parameter is a boolean, true if the constraint must not exceed the most significant part of the actual value, and false otherwise. The fourth parameter is a boolean, true if the operation is *greater than*, and false if *less than*. The fifth parameter is a boolean, true if the operation is *equal to*, and false otherwise (e.g., if the operation is *greater than or equal to*, both the fourth and fifth parameters would be true).

The return value is a boolean, true if the constraint is met, and false if not.

#### opEqualTo method.

This method is public in case the need to call it directly arises, but you should generally just call `singleCompare` or `multiCompare` instead.

After `singleCompare` has determined which kind of operation is needed, `opVersions` and `opEqualTo` are used internally to perform the operation in question. In particular, `opEqualTo` is responsible for handling any *exactly equal to* operations.

```PHP
public function opEqualTo(string $Actual, string $Constraint): bool
```

`opEqualTo` accepts 2 parameters. The first parameter is a string, the actual value being compared. The second parameter is a string, the constraint the actual value being compared must match against.

The return value is a boolean, true if the constraint is met, and false if not.

#### multiCompare method.

`multiCompare` provides a convenient way to match multiple values against multiple constraints with a single method call. Depending on the nature of your implementation, it may be easier to simply call `singleCompare` multiple times, or you may prefer to be able to perform all operations together, relying on a combined return value. Its two accepted parameters are both arrays, with elements corresponding to the accepted parameters for `singleCompare`.

```PHP
public function multiCompare(array $Operand, array $Prefix): bool
```

As an example, the following arbitrary code:

```PHP
if ($Object->singleCompare('1.2.3', '^1') && $Object->singleCompare('2.3.4', '>=2.3 <4') && $Object->singleCompare('3.4.5', '^1|^3')) {
    // Do something.
}
```

..is entirely equivalent to:

```PHP
if ($Object->multiCompare(['1.2.3', '2.3.4', '3.4.5'], ['^1', '>=2.3 <4', '^1|^3'])) {
    // Do something.
}
```

#### singleCompare method.

`singleCompare` is the method you'll generally want to call whenever you want to perform a comparison operation.

A comparison operation performed by `singleCompare` can generally be thought of as a mathematical comparison operation, whereby the first parameter is one's *left operand*, and the second parameter, the *prefix*, is one's *operator* and *right operand* combined together, or may be a combination of any number of *operators* and *right operands* combined together, which'll always be operated against the singular defined *left operand* of the first parameter. The *prefix* forms a *constraint* by which the *operand* must match, in order for the method to return true, returning false when the operand fails to match the specified constraint.

```PHP
public function singleCompare(string $Operand, string $Prefix): bool
```

Operands may be composed of integers (whole numbers), of "PHP-standardised" version numbers (including with identifiers such as "alpha", "beta", "dev", "rc", etc), of "semver" (semantic versioning) version numbers, or some variant of integers delimited by decimals/periods. Since each operand is split by its decimals/periods, each part treated as a whole number in its own right, it should be noted, e.g., that `1.02` would be regarded as equal to `1.2`, and greater than `1.1`. To compare according to each unit position (i.e., what one would typically expect for when comparing fractions, floats, etc), it may be necessary to break each such unit by additional decimals/periods (e.g., `1.0.2` instead of `1.02`).

The number of allowed parts is not limited, theoretically able to go on forever (`1.2.3.4.5.6.7.8.9` ...etc), pending system limitations and the limitations of common sense.

Operators recognised are as follows:

Operator | Description
---|---
`>` | *Greater than.*
`>=` | *Greater than or equal to.*
`<` | *Less than.*
`<=` | *Less than or equal to.*
`^` | *Greater than or equal to, but less than the next most significant. E.g., `^1.2.3` would effectively be the same as `>=1.2.3 <2.0.0`.*
`=` | *Exactly equal to. Note that since all value parts are normalised to integers, something like `1.02.003` would be regarded as "exactly equal to" `1.2.3`.*

When no operator is specified, or when the specified operator isn't recognised, the operation will default to *exactly equal to* (albeit that results may vary from in the event that the unrecognised operator is regarded as part of the operand itself, which likely isn't what would be desired in most cases).

Some examples:

```PHP
$Object->singleCompare('1.2.3', '^1');
$Object->singleCompare('1.2.3', '^2');
$Object->singleCompare('1.2.3', '>=1 <2');
$Object->singleCompare('2.3.4', '>=2.3 <4');
$Object->singleCompare('3.4.5', '^1|^3');
$Object->singleCompare('4.5.6', '<4');
$Object->singleCompare('4.5.6', '<=4');
$Object->singleCompare('4.5.6', '<=5');
$Object->singleCompare('4.5.6', '4.5.6');
```

Results:

```
true
false
true
true
true
false
false
true
true
```

#### splitVersionParts method.

This method is public in case the need to call it directly arises, but it should be regarded as effectively private for the purposes of general implementation.

`splitVersionParts` is used internally by `opVersions` and `opEqualTo` to split a version into its constituent parts.

```PHP
public function splitVersionParts(string $Version = ''): array
```

Some examples:

```PHP
var_dump($Object->splitVersionParts('1.2.3'));
var_dump($Object->splitVersionParts('2.3.4-DEV+123456'));
var_dump($Object->splitVersionParts('2021.04.23.2143-RC5'));
var_dump($Object->splitVersionParts('--3-4-5,6,7,8..9-rawr!!?!?!111ii!--foo'));
```

Results:

```
array(3) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
}

array(5) {
  [0]=>
  int(2)
  [1]=>
  int(3)
  [2]=>
  int(4)
  [3]=>
  int(-5)
  [4]=>
  int(123456)
}

array(6) {
  [0]=>
  int(2021)
  [1]=>
  int(4)
  [2]=>
  int(23)
  [3]=>
  int(2143)
  [4]=>
  int(-2)
  [5]=>
  int(5)
}

array(11) {
  [0]=>
  int(0)
  [1]=>
  int(0)
  [2]=>
  int(3)
  [3]=>
  int(4)
  [4]=>
  int(5)
  [5]=>
  int(0)
  [6]=>
  int(9)
  [7]=>
  int(-2)
  [8]=>
  int(-6)
  [9]=>
  int(0)
  [10]=>
  int(-6)
}
```

The last of those four examples is highly nonsensical, but it's useful nonetheless to demonstrate here the results of such nonsensical inputs, in order to provide a better understanding of how the class behaves.

#### dataTraverse method.

`dataTraverse` provides a way to traverse an array, teasing out specific elements as needed, utilising a simplified imitation of dot notation.

```PHP
public function dataTraverse(&$Data, $Path = [])
```

`dataTraverse` accepts 2 parameters. The first parameter is the array to be traversed, passed by reference. The second parameter accepts an array or a string string, and is the path utilising dot notation (as aforementioned). `dataTraverse` is recursive, the initial call typically taking in a string, at which point it breaks it down into an array to be passed to its recursions (an empty array and an empty string should elicit the same return values).

In addition, the PHP functions `trim()`, `strtolower()`, `strtoupper()`, and `strlen()` will be recognised and may optionally be used as the tail of the second parameter.

Some examples:

```PHP
$Arr = [
    'Foo' => [
        'Bar' => ['Hello' => 'World', 'Goodbye' => 'Cruel World'],
        'Baz' => 'Hello World!'
    ],
    'Far' => ['Boo' => 'To You'],
    'Plenty of space' => '   ...yep.'
];

var_dump($Object->dataTraverse($Arr, 'Foo.Bar.Hello'));
var_dump($Object->dataTraverse($Arr, 'Foo.Bar.Goodbye'));
var_dump($Object->dataTraverse($Arr, 'Foo.Bar.strlen()'));
var_dump($Object->dataTraverse($Arr, 'Foo.Baz'));
var_dump($Object->dataTraverse($Arr, 'Foo.Baz'));
var_dump($Object->dataTraverse($Arr, 'Foo.Baz.strtoupper()'));
var_dump($Object->dataTraverse($Arr, 'Foo.Baz.strtolower()'));
var_dump($Object->dataTraverse($Arr, 'Plenty of space'));
var_dump($Object->dataTraverse($Arr, 'Plenty of space.trim()'));
var_dump($Object->dataTraverse($Arr, 'This element does not exist'));
var_dump($Object->dataTraverse($Arr, 'Foo.Bar.Hello.Element is not an array'));
```

Results:

```
string(5) "World"
string(11) "Cruel World"
int(11)
string(12) "Hello World!"
string(12) "HELLO WORLD!"
string(12) "hello world!"
string(10) "   ...yep."
string(7) "...yep."
string(0) ""
string(5) "World"
```

As shown by the final two examples, the array will be traversed only as far as the path allows. If an instruction of the path points to a non-existent index, the instruction will be ignored, continuing the instructions until none are left. At that time, if the current index provides a scalar value, that scalar value will be returned. If a scalar value isn't available, or if the path goes nowhere, an empty string will be returned. An erroneous path may return a value other than expected, but shouldn't produce any overt errors.

#### ifCompare method.

`ifCompare` provides a mechanism to perform some limited, basic, rudimentary if/then/else logic directly from strings. This can be useful in situations where writing if/then/else logic directly with PHP code, or with other kinds of code, mightn't be possible, or in situations where the full scope of what should be possible or permissible needs to be limited to just such limited, basic, rudimentary if/then/else logic only, or needs to be determined directly from a string.

```PHP
public function ifCompare(&$Data, string $IfString): string
```

`ifCompare` accepts 3 parameters. The first parameter, passed by reference, would typically be an array, but may be any scalar data type. When `ifCompare` leverages `dataTraverse`, this first parameter is the data that it traverses over. The second parameter is a string, and contains the actual if/then/else logic to be processed. The return value is a string, the results of the operation.

It should be noted that `eval()` and `exec()` *don't* exist anywhere in this class. Numerous security risks associated with using such PHP functions, along with using such PHP functions aside from when strictly necessary being widely regarded as bad practice, are more than enough reasons to not use them.

`ifCompare` always processes logic directly from left-to-right (no BIMDAS/BODMAS support, bracketing/bracing, etc), and it utilises *curly brackets* `{}` as a means to indicate the need to use dot notation.

Some examples:

```PHP
$Arr = [
    'Foo' => ['Bar' => 'Hello', 'Baz' => 'Goodbye'],
    'Numbers' => ['One' => 1, 'Ten' => 10, 'Hundred' => 100],
    'Versions' => ['First' => '1.2.3', 'Second' => '2.3.4']
];

var_dump($Object->ifCompare($Arr, 'if {Versions.Second}^2.3 then {Foo.Bar} else {Foo.Baz}'));
var_dump($Object->ifCompare($Arr, 'if {Versions.Second}^2.3 thenif {Versions.First}^1.2 then Success else Failure'));
var_dump($Object->ifCompare($Arr, 'if {Versions.Second}^1.2 thenif {Versions.First}^3.4 then Success else Failure'));
var_dump($Object->ifCompare($Arr, 'if 1>2 then WTH? else Yeah, sounds right.'));
var_dump($Object->ifCompare($Arr, 'if {Foo.Baz}===Goodbye then Sayonara else Ohayogozaimasu'));
var_dump($Object->ifCompare($Arr, 'if {Versions.Second}==={Versions.First} then They are the same else They are different'));
```

Results:

```
string(5) "Hello"
string(7) "Success"
string(7) "Failure"
string(19) "Yeah, sounds right."
string(8) "Sayonara"
string(18) "They are different"
```

If incorrect logic/syntax is used, or if the string isn't properly understood, an empty string will, in most cases, be returned.

If more complex usage is needed in the future, the capabilities of this class can always be further built upon at that time, but since it's best to avoid needless overengineering, and since the class already satisfies the needs of the implementations it was originally intended for, building this class further isn't anticipated at this time.

---


Last Updated: 23 April 2021 (2021.04.23).
