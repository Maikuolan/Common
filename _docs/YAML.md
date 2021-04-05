### Documentation for the "YAML" class.

*Used by CIDRAM and phpMussel to handle YAML data, the YAML class is a simple YAML handler intended to adequately serve the needs of the packages and projects where it is implemented. Note however, that it isn't a complete YAML solution, instead supporting the YAML specification only to the bare minimum required by those packages and projects known to implement it, and I therefore can't guarantee that it'll necessarily be an ideal solution for other packages and projects, whose exact requirements I mightn't be familiar with.*

---


### How to use:

The YAML class can process YAML data either during instantiation, or after instantiation. If you specifically need to validate YAML data, without necessarily needing to actually work with it, or if you need to run tests using the YAML class, it's better to have the class process YAML data after instantiation. Otherwise, if you just need to process YAML data in order to work with it, and don't necessarily need to validate anything and don't necessarily need to run any tests, it's better to have the class process YAML data during instantiation.

Assuming an example YAML file as follows:

```YAML
# An example YAML file.

String foo: "Bar"
Integer foo: 1234
Float foo: 123.4
Example numeric array:
 - "Bar1"
 - "Bar2"
 - "Bar3"
 - "Bar4"
Example associative array:
 Foo1: "Bar1"
 Foo2: "Bar2"
 Foo3: "Bar3"
 Foo4: "Bar4"
Example mixed multi-dimensional array:
 - "Bar1"
 - "Bar2"
 xFooX: "xBarX"
 Some int: 4567
 Sub array:
  Hello: "World"
  Sub-sub array:
   Foobar: "Barfoo"
Example hex-encoded data: 0x48656c6c6f20576f726c64202862757420696e2068657829
Multi-line example: |
 h e l l o - w o r l d
 :_   _      _ _         _    _            _     _
 | | | |    | | |       | |  | |          | |   | |
 | |_| | ___| | | ___   | |  | | ___  _ __| | __| |
 |  _  |/ _ \ | |/ _ \  | |/\| |/ _ \| '__| |/ _` |
 | | | |  __/ | | (_) | \  /\  / (_) | |  | | (_| |
 \_| |_/\___|_|_|\___/   \/  \/ \___/|_|  |_|\__,_|
# Anyway, I think you get the idea...
```

An example of processing that YAML data during instantiation:

```PHP
<?php
// First, let's fetch the example YAML file's raw data.
$RawYAML = file_get_contents(__DIR__ . '/example.yaml');

// Then, we'll instantiate the new YAML object.
$Object = new \Maikuolan\Common\YAML($RawYAML);

// The actual processed data will be contained by the public member "Data". We'll var_dump it to show its contents.
var_dump($Object->Data);
```

An example of processing that YAML data after instantiation:

```PHP
<?php
// First, let's fetch the example YAML file's raw data.
$RawYAML = file_get_contents(__DIR__ . '/example.yaml');

// Then, we'll instantiate the new YAML object.
$Object = new \Maikuolan\Common\YAML();

// Now, to process the raw YAML data.
$Object->process($RawYAML, $Object->Data);

// The actual processed data will be contained by the public member "Data". We'll var_dump it to show its contents.
var_dump($Object->Data);
```

In both cases, the expected output (which should be the same):

```
array(8) {
  ["String foo"]=>
  string(3) "Bar"
  ["Integer foo"]=>
  int(1234)
  ["Float foo"]=>
  float(123.4)
  ["Example numeric array"]=>
  array(4) {
    [0]=>
    string(4) "Bar1"
    [1]=>
    string(4) "Bar2"
    [2]=>
    string(4) "Bar3"
    [3]=>
    string(4) "Bar4"
  }
  ["Example associative array"]=>
  array(4) {
    ["Foo1"]=>
    string(4) "Bar1"
    ["Foo2"]=>
    string(4) "Bar2"
    ["Foo3"]=>
    string(4) "Bar3"
    ["Foo4"]=>
    string(4) "Bar4"
  }
  ["Example mixed multi-dimensional array"]=>
  array(5) {
    [0]=>
    string(4) "Bar1"
    [1]=>
    string(4) "Bar2"
    ["xFooX"]=>
    string(5) "xBarX"
    ["Some int"]=>
    int(4567)
    ["Sub array"]=>
    array(2) {
      ["Hello"]=>
      string(5) "World"
      ["Sub-sub array"]=>
      array(1) {
        ["Foobar"]=>
        string(6) "Barfoo"
      }
    }
  }
  ["Example hex-encoded data"]=>
  string(24) "Hello World (but in hex)"
  ["Multi-line example"]=>
  string(326) "h e l l o - w o r l d
:_   _      _ _         _    _            _     _
| | | |    | | |       | |  | |          | |   | |
| |_| | ___| | | ___   | |  | | ___  _ __| | __| |
|  _  |/ _ \ | |/ _ \  | |/\| |/ _ \| '__| |/ _` |
| | | |  __/ | | (_) | \  /\  / (_) | |  | | (_| |
\_| |_/\___|_|_|\___/   \/  \/ \___/|_|  |_|\__,_|"
}
```

The reason for the difference, is quite simple: The process method will return true or false, depending on whether it was able to successfully process the YAML data (whereas a constructor doesn't have any return value). This means that you can use the return value of the process method to validate whether the input (the first parameter of the process method) is valid YAML data (valid, at least, to the extent supported by the class; an important distinction, given that the class is designed to support the YAML specification only to the bare minimum extent required by its original implementations, CIDRAM and phpMussel).

The process method actually supports three parameters:

```PHP
public function process(string $In, array &$Arr, int $Depth = 0);
```

The second parameter of the process method, generally, should always point to the `Data` member of the same object. The `Data` member is an array intended specifically for holding the processed YAML data. It's possible to point it elsewhere without causing problems, and pointing it elsewhere could be necessary for some implementations, but keeping all the object's own data self-contained is generally a cleaner, more recommended approach.

The third parameter should never be populated by the implementation. The process method can call itself recursively, and the third parameter is populated during such recursive calls by the method itself.

##### *Can the reverse be done, too? Can an array be converted into YAML data?*

Yes. To do this, use the reconstruct method. The reconstruct method supports only one parameter:

```PHP
public function reconstruct(array $Arr);
```

This parameter is the array that you want converted into YAML data. The method returns a string (the YAML data). If you want to convert the object's own self-contained, already processed YAML data, just use the object's `Data` member as the reconstruct method's parameter.

As an example:

```PHP
<?php
// An example array.
$Array = [
    'foo' => 'bar',
    1 => 'abc',
    2 => 'def',
    3 => 'ghi',
    'jkl' => 4,
    'mno' => 5,
    'pqr' => 6,
    7 => 8,
    9 => 0,
    'boolVal' => false,
    'boolVal2' => true,
    'multidimensional array' => [
        'hello' => 'world',
        'hello2' => 'world2'
    ],
    'hello3' => 'world3'
];

// Instantiating a new YAML object.
$Object = new \Maikuolan\Common\YAML();

// "Reconstructing" some new YAML data, using our example array above.
$NewData = $Object->reconstruct($Array);

// We'll echo it, to show its contents.
echo $NewData;
```

The expected output:
```
foo: "bar"
1: "abc"
2: "def"
3: "ghi"
jkl: 4
mno: 5
pqr: 6
7: 8
9: 0
boolVal: false
boolVal2: true
multidimensional array:
 hello: "world"
 hello2: "world2"
hello3: "world3"
```

We can attempt to reverse this data, to demonstrate consistency between how the class processes YAML data into a usable array, and how the class reconstructs YAML data from an array.

```PHP
<?php
$YAML = 'foo: "bar"
1: "abc"
2: "def"
3: "ghi"
jkl: 4
mno: 5
pqr: 6
7: 8
9: 0
boolVal: false
boolVal2: true
multidimensional array:
 hello: "world"
 hello2: "world2"
hello3: "world3"

';

$Object = new \Maikuolan\Common\YAML($YAML);

echo 'The YAML data, processed into an array:' . PHP_EOL;
var_dump($Object->Data);

echo PHP_EOL . PHP_EOL;

$Reconstructed = $Object->reconstruct($Object->Data);

echo 'Is the reconstructed YAML data, and the original YAML data, the same? ';

echo $YAML === $Reconstructed ? 'Yes.' : 'No.';
```

The expected output:
```
The YAML data, processed into an array:
array(13) {
  ["foo"]=>
  string(3) "bar"
  [1]=>
  string(3) "abc"
  [2]=>
  string(3) "def"
  [3]=>
  string(3) "ghi"
  ["jkl"]=>
  int(4)
  ["mno"]=>
  int(5)
  ["pqr"]=>
  int(6)
  [7]=>
  int(8)
  [9]=>
  int(0)
  ["boolVal"]=>
  bool(false)
  ["boolVal2"]=>
  bool(true)
  ["multidimensional array"]=>
  array(2) {
    ["hello"]=>
    string(5) "world"
    ["hello2"]=>
    string(6) "world2"
  }
  ["hello3"]=>
  string(6) "world3"
}


Is the reconstructed YAML data, and the original YAML data, the same? Yes.
```

---


### Supported data types:

The YAML class supports arrays, integers, floats, booleans (`true`, `+`, `false`, `-`), null (`null`, `~`), strings, multi-line strings (`|`), folded multi-line strings (`>`), and hexadecimal data (`0x`).

The YAML class does not support callables, closures, or objects. If objects, closures, or callables are supplied to reconstruct, a fatal error will occur. Don't ever do this.

The YAML class allows YAML data to contain comments. The YAML class considers all data, beginning with a non-escaped hash (or number sign; `#`), and ending at any valid line ending (e.g., `\n`, `\r`), to be a comment. Therefore, all hashes, within any valid strings, within any unprocessed YAML data, should be escaped (i.e., `\#`), in order to be processed correctly by the YAML class.

Within unprocessed YAML data, integers, floats, booleans, null, and hexadecimal data should never be quoted. Quoting for strings is optional, and it generally doesn't matter whether you choose to quote strings (i.e., quoting for strings is not strict). However, strings should always be quoted if the intended data type would otherwise be ambiguous. For example, `Foo: "false"`, `Foo: "123"`, and `Foo: "12.3"` would all result in strings, whereas `Foo: false`, `Foo: 123`, and `Foo: 12.3` would result in a boolean, an integer, and a float respectively. Quoting for keys is treated in the same manner as quoting for values.

Also, when reconstructing YAML data, string values are always quoted, whereas keys (regardless of data type) are never quoted. Therefore, if you ever need to reverse-process YAML data for any reason (i.e., process some YAML data, and then reconstruct the resulting array back into YAML data again; e.g., for testing purposes), you should also always quote string values (i.e., approach value quoting strictly), should never quote keys, and should never use "true", "false", or "null" as names for keys (because unquoted, they'll look like booleans or null, and neither booleans nor null can be used as the names of array keys in PHP, meaning that you'll need to quote them to forcefully identify them as strings, but the reconstruct method would unquote them when reconstructing the data, causing an inconsistency between the original YAML data and the reconstruted YAML data).

---


Last Updated: 19 February 2021 (2021.02.19).
