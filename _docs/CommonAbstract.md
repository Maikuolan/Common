### Documentation for the "CommonAbstract" class.

*Common abstract for the common classes package. Not to be instantiated directly; To be extended by the other classes.*

---


### Contents:

- [dataTraverse method.](#datatraverse-method)

#### dataTraverse method.

`dataTraverse` provides a way to recursively traverse an array or an object, teasing out specific elements or properties as needed, utilising a simplified imitation of dot notation.

```PHP
public function dataTraverse(&$Data, $Path = [], bool $AllowNonScalar = false, bool $AllowMethodCalls = false)
```

`dataTraverse` accepts 4 parameters. The first parameter is the array or object to be traversed, passed by reference. The second parameter is an optional array or string, and provides the path which utilises dot notation (as aforementioned). The third parameter is an optional boolean to indicate whether to allow the method to return non-scalar values (`true` to allow non-scalar values; `false` to prohibit non-scalar values; `false` by default). The fourth parameter is an optional boolean to indicate whether to allow the method to perform method calls on traversed objects (`true` to allow method calls; `false` to prohibit method calls; `false` by default).

In addition, the PHP functions `trim()`, `strtolower()`, `strtoupper()`, and `strlen()` will be recognised and may optionally be used as the tail of the second parameter.

I would recommended to not traverse untrusted data, but if you must do so, then I would recommend to prohibit method calls (i.e., let the third parameter remain `false`).

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
var_dump($Object->dataTraverse($Arr, 'Foo.Bar.Goodbye.strlen()'));
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

---


Last Updated: 14 September 2023 (2023.09.14).
