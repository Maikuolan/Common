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
- [Examples.](#examples)

#### Matrix member.

The Matrix member will be populated with a multidimensional array (the "matrix") when createMatrix is called, and could be fairly described as the instance's main concern. Various methods are provided for accessing and manipulating this multidimensional array, and in case one would prefer to access or manipulate this data directly at the implementation, the member is defined as public.

```PHP
public $Matrix = [];
```

#### Dimensions member.

The Dimensions member is an integer which describes the number of dimensions the matrix has. It is populated when createMatrix is called, and is defined as public.

```PHP
public $Dimensions = 1;
```

#### Magnitude member.

The Magnitude member is an integer, or an array of integers, which describes the magnitude of each dimension of the matrix. It is populated when createMatrix is called, and is defined as public. When populated with an integer, that integer determines the magnitude of all dimensions. When populated with an array of integers, each element of the array corresponds to each dimension of the matrix respectively (i.e., the first element determines the magnitude of the first dimension, the second element of the second dimension and so on).

```PHP
public $Magnitude = 1;
```

#### Data member.

The Data member can be any type of data supported by PHP, is populated when createMatrix is called, and is defined as public. It will be used to populate each coordinate of the matrix (i.e., when populating the Matrix member, the contents of the Data member will be used to populate each element of the deepest vector of the matrix).

```PHP
public $Data = [];
```

#### createMatrix method.

The createMatrix method is used to create a new matrix for the instance. It accepts three parameters. The first parameter, `$Dimensions`, describes the number of dimensions the matrix should have, and must be an integer (refer to the Dimensions member). The second parameter, `$Magnitude`, describes the magnitude that each dimension of the matrix should have, and must be either an integer or an array of integers (refer to the Magnitude member). The third parameter, `$Data`, describes the default data that each coordinate should be populated with (refer to the Data member). It has no result value.

```PHP
public function createMatrix(int $Dimensions, $Magnitude, $Data)
```

#### populateVector method.

The populateVector method is invoked by the createMatrix method, and recursively by itself, and is used to populate each vector when creating a new matrix for the instance. It accepts two parameters. The first parameter, `$Vector`, is the particular vector to be populated, passed by reference. The second parameter, `$Dimension`, is an integer incremented at each recursion, describing to the method which dimension, as a number, is to be populated, allowing the recursion to cease upon reaching the total number of dimensions the matrix should have, and allowing the method to correctly determine the magnitude intended for each vector. It has no result value.

```PHP
private function populateVector(array &$Vector, int $Dimension)
```

#### iterateCallback method.

The iterateCallback method allows applying a callback function over some specified coordinates. It accepts a minimum of one parameter. The first parameter, `$Description`, describes the particular coordinates where the callback should be applied, and can be an integer or a string. The second parameter, `$Callback`, is the callback function to be applied. The second parameter is optional, and when omitted, defaults to returning the value of the coordinate from where it is to be applied. Any subsequently provided parameters are accepted as an optional, third, variadic parameter, `$Data`, which is then provided to the callback function at each call. The return value of the iterateCallback method is determined by the callback function applied, which generally means an array of return values from the callback function itself in relation to its application over the specified coordinates.

```PHP
public function iterateCallback($Description, callable $Callback = null, ...$Data)
```

#### iterateCallbackGenerator method.

The iterateCallbackGenerator method is a generator invoked by the iterateCallback method, and serves as a simple wrapper for the recursively invoked iterateCallbackGeneratorInner method. It accepts three parameters. The first parameter, `$Indexes`, is an array describing the coordinates to iterate over. The second parameter, `$Callback`, is the callback function to apply when iterating over the specified coordinates. The third parameter, `$Data`, is an array containing any optional, variadic supplied by iterateCallback.

```PHP
private function iterateCallbackGenerator(array $Indexes, callable $Callback, array $Data): \Generator
```

#### iterateCallbackGeneratorInner method.

The iterateCallbackGeneratorInner method is a generator invoked by the iterateCallbackGenerator method, and recursively by itself, and is the main mechanism by which the iterateCallback method is able to apply a callback function to some specified coordinates, and the main mechanism by which return values can be firstly yielded from the callback function, and then subsequently returned by the iterateCallback method to the implementation. It accepts six parameters. The first parameter, `$Matrix`, is the matrix or particular vector from where the current iteration is occurring, passed by reference. The second parameter, `$Indexes`, is an array describing the particular coordinates where the iteration is occurring, passed by reference. The third parameter, `$Depth`, is an integer describing the current depth of the recursion, necessary for determing which specific coordinates within the vector are to be iterated over. The fourth parameter, `$KeyRoot`, is a string used to aid in supplying the correct keys for any return values yielded from the callback function. The fifth parameter, `$Callback`, is the callback function. The sixth parameter, `$Data`, is an array containing any optional, variadic supplied by iterateCallback.

```PHP
private function iterateCallbackGeneratorInner(array &$Matrix, array &$Indexes, int $Depth, string $KeyRoot = '', callable $Callback, array $Data): \Generator
```

#### Examples.

An example for generating a simple two-dimensional matrix, whereby each vector has a magnitude of three, and applying a simple callback function to it:

```PHP
$Matrix = new \Maikuolan\Common\Matrix();
$Matrix->createMatrix(2, 3, 'Foo');
$Number = 1;
$Matrix->iterateCallback('0-2,0-2', function (&$ThisValue) use (&$Number) {$ThisValue = $Number++;});
var_dump($Matrix->Matrix);
```

Produces:

```
array(3) {
  [0]=>
  array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  [1]=>
  array(3) {
    [0]=>
    int(4)
    [1]=>
    int(5)
    [2]=>
    int(6)
  }
  [2]=>
  array(3) {
    [0]=>
    int(7)
    [1]=>
    int(8)
    [2]=>
    int(9)
  }
}
```

Because that matrix has exactly two dimensions, its values could be described in a table like so:

1 | 2 | 3
---|---|---
4 | 5 | 6
7 | 8 | 9

The "dimensions", in this example, could be described as the columns and rows of the table, or as "up" and "down", with each individual cell in the table described as a "coordinate". Each dimension ("up" or "down"), in combination with the specified "magnitude" (in this case, three) forms a "vector", which could also be regarded as the values of a particular set of coordinates (e.g.; 1, 2, 3; 4, 5, 6; 7, 8, 9; 1, 4, 7; 2, 5, 8; 3, 6, 9; etc).

However, the Matrix class isn't aware of any particular notion of "rows" and "columns", of "up" and "down", of "left" and "right", of "forward" and "backward" or of anything else like that; such terms are useful as a means of describing a particular vector to a human, but for the Matrix class, which can tolerate any arbitrary number of dimensions and any arbitrary magnitude (available RAM, processor cycles, and time permitting), such words are generally meaningless, and it may be easier to simply describe vectors in terms of belonging to the first dimension, the second dimension, the third dimension and so on.

---


Last Updated: 9 March 2020 (2020.03.09).
