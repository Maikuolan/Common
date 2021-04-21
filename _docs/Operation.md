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

`opVersions` accepts 5 parameters. The first parameter is a string, the actual value being compared. The second parameter is a string, the constraint the actual value being compared must match against. The third parameter is a boolean, true if the constraint must not exceed the most significant part of the actual value, and false otherwise. The fourth parameter is a boolean, true if the operation is *greater than*, and false if *less than*. The fifth parameter is a boolean, true if the operation is *equal to*, and false otherwise (e.g., if the operation is *greater than or equal to*, both the fourth and fifth parameters would be true).

The return value is a boolean, true if the constraint is met, and false if not.

```PHP
public function opVersions(string $Actual, string $Constraint, bool $NextMajor, bool $GreaterThan, bool $OrEqualTo): bool
```

#### opEqualTo method.

This method is public in case the need to call it directly arises, but you should generally just call `singleCompare` or `multiCompare` instead.

After `singleCompare` has determined which kind of operation is needed, `opVersions` and `opEqualTo` are used internally to perform the operation in question. In particular, `opEqualTo` is responsible for handling any *exactly equal to* operations.

`opEqualTo` accepts 2 parameters. The first parameter is a string, the actual value being compared. The second parameter is a string, the constraint the actual value being compared must match against.

The return value is a boolean, true if the constraint is met, and false if not.

```PHP
public function opEqualTo(string $Actual, string $Constraint): bool
```

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

Operands may be composed of integers (whole numbers), of "PHP-standardised" version numbers, of "semver" (semantic versioning) version numbers, or some variant of integers delimited by decimals/periods. Since each operand is split by its decimals/periods, each segment treated as a whole number in its own right, it should be noted that, e.g., `1.02` would be regarded as equal to `1.2`, and greater than `1.1`. To compare according to each unit position (i.e., what one would typically expect for when comparing fractions, floats, etc), it may be necessary to break each such unit by additional decimals/periods (e.g., `1.0.2` instead of `1.02`).

The number of allowed segments is not limited, theoretically able to go forever (`1.2.3.4.5.6.7.8.9 ...`, etc), pending system limitations and the limitations of common sense.

Operators recognised are as follows:

Operator | Description
---|---
`>` | *Greater than.*
`>=` | *Greater than or equal to.*
`<` | *Less than.*
`<=` | *Less than or equal to.*
`^` | *Greater than or equal to, but less than the next most significant.*
`=` | *Exactly equal to.*

When no operator is specified, or the specified operator isn't recognised, the operation will default to *exactly equal to*.

```PHP
public function singleCompare(string $Operand, string $Prefix): bool
```

#### splitVersionParts method.

*To-do.*

```PHP
public function splitVersionParts(string $Version = ''): array
```

#### dataTraverse method.

*To-do.*

```PHP
public function dataTraverse(&$Data, $Path = [])
```

#### ifCompare method.

*To-do.*

```PHP
public function ifCompare(&$Data, string $IfString): string
```

---


Last Updated: 21 April 2021 (2021.04.21).
