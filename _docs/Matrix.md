### Documentation for the "Matrix" class.

*Facilitates the generation of multidimensional arrays to an arbitrarily specified depth and number of elements, and facilitates iteration through those multidimensional arrays in any direction (whether up and down a particular a array, across different depths, etc) via arbitrary callables and closures.*

---


### How to use:

- [Matrix member.](#matrix-member)
- [Dimensions member.](#dimensions-member)
- [Magnitude member.](#magnitude-member)
- [Data member.](#data-member)
- [createMatrix method.](#creatematrix-method)
- [populateVector method.](#populatevector-method)
- [iterateCallback method.](#iteratecallback-method)
- [iterateCallbackGenerator method.](#iteratecallbackgenerator-method)
- [iterateCallbackGeneratorInner method.](#iteratecallbackgeneratorinner-method)

#### Matrix member.

Lorem ipsum.

```PHP
public $Matrix = [];
```

#### Dimensions member.

Lorem ipsum.

```PHP
public $Dimensions = 1;
```

#### Magnitude member.

Lorem ipsum.

```PHP
public $Magnitude = 1;
```

#### Data member.

Lorem ipsum.

```PHP
public $Data = [];
```

#### createMatrix method.

Lorem ipsum.

```PHP
public function createMatrix(int $Dimensions, $Magnitude, $Data)
```

#### populateVector method.

Lorem ipsum.

```PHP
private function populateVector(array &$Vector, int $Dimension)
```

#### iterateCallback method.

Lorem ipsum.

```PHP
public function iterateCallback($Description, callable $Callback = null, ...$Data)
```

#### iterateCallbackGenerator method.

Lorem ipsum.

```PHP
private function iterateCallbackGenerator(array $Indexes, callable $Callback, array $Data): \Generator
```

#### iterateCallbackGeneratorInner method.

Lorem ipsum.

```PHP
private function iterateCallbackGeneratorInner(array &$Matrix, array &$Indexes, int $Depth, string $KeyRoot = '', callable $Callback, array $Data): \Generator
```

---


Last Updated: 24 January 2020 (2020.01.24).
