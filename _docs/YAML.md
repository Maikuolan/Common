### Documentation for the "YAML" class (a.k.a., the "YAML handler").

*Used by the CIDRAM and phpMussel projects to handle YAML data.*

---


### Content.

- [Introduction.](#introduction)
- [How to use.](#how-to-use)
- [Reconstruction.](#reconstruction)
- [Public class members.](#public-class-members)
- [Supported data types.](#supported-data-types)
- [Comments and implicit typing.](#comments-and-implicit-typing)
- [Anchors, aliases, and inline variables.](#anchors-aliases-and-inline-variables)
- [Supported from the specification.](#supported-from-the-specification)
- [Additionally supported.](#additionally-supported)

---


### Introduction

Currently, the YAML handler follows the [version 1.2.2 of the YAML Ain't Markup Language specification](https://yaml.org/spec/1.2.2/). If this changes in the future (e.g., a new version of the specification is released and the YAML handler is subsequently updated in order to adhere to that new version), this documentation will be updated accordingly.

A reasonable attempt has been made to adhere to the specification, which the YAML handler does correctly for the most part, although not in entirety (supported features, types, tags, syntax, etc will be detailed towards the end of this documentation).

Information regarding the language-independent YAML tags recommended for implementation across applications (used as a guideline for the implementation of tags to the YAML handler) is sourced from [version 1.1 of the Language-Independent Types for YAML](https://yaml.org/type/) document.

---


### How to use.

The YAML handler can process YAML data either during or after instantiation. It's up to you which strategy you'd prefer to use, and which is best to use will depend on the needs of your implementation.

Using the following YAML data as an example:

```YAML
String foo: "Bar"
Integer foo: 1234
Float foo: 123.4
Example implicit numeric array:
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
 0: "Bar0"
 1: "Bar1"
 xFooX: "xBarX"
 Some int: 4567
 Sub array:
  Hello: "World"
  Sub-sub array:
   Foobar: "Barfoo"
Multi-line example: |
 h e l l o - w o r l d
 hello-world
Example booleans and null:
 This is true: true
 This is false: false
 This is null: null
Testing anchors:
 Anchored text push: &TestAnchor "Some placeholder text."
 Anchored text pull: *TestAnchor
Escaping test: "Our number is \#123-456-789."
End of file: ":-)"
```

An example of processing that YAML data during instantiation:

```PHP
<?php
// First, let's fetch the example YAML file's raw data.
$RawYAML = file_get_contents(__DIR__ . '/reconstruct.yaml');

// Then, we'll instantiate the new YAML object.
$Object = new \Maikuolan\Common\YAML($RawYAML);

// The actual processed data will be contained by the public member "Data". We'll use var_dump to show its contents.
var_dump($Object->Data);
```

An example of processing that YAML data after instantiation:

```PHP
<?php
// First, let's fetch the example YAML file's raw data.
$RawYAML = file_get_contents(__DIR__ . '/reconstruct.yaml');

// Then, we'll instantiate the new YAML object.
$Object = new \Maikuolan\Common\YAML();

// Now, to process the raw YAML data.
$Object->process($RawYAML, $Object->Data);

// The actual processed data will be contained by the public member "Data". We'll use var_dump to show its contents.
var_dump($Object->Data);
```

In both cases, the expected output (which should be the same):

```
array(14) {
  ["String foo"]=>
  string(3) "Bar"
  ["Integer foo"]=>
  int(1234)
  ["Float foo"]=>
  float(123.4)
  ["Example implicit numeric array"]=>
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
    string(4) "Bar0"
    [1]=>
    string(4) "Bar1"
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
  ["Multi-line example"]=>
  string(33) "h e l l o - w o r l d
hello-world"
  ["Example booleans and null"]=>
  array(3) {
    ["This is true"]=>
    bool(true)
    ["This is false"]=>
    bool(false)
    ["This is null"]=>
    NULL
  }
  ["Testing anchors"]=>
  array(2) {
    ["Anchored text push"]=>
    string(22) "Some placeholder text."
    ["Anchored text pull"]=>
    string(22) "Some placeholder text."
  }
  ["Escaping test"]=>
  string(27) "Our number is #123-456-789."
  ["Hexadecimal number notation"]=>
  int(65536)
  ["Binary number notation"]=>
  int(16)
  ["Octal number notation"]=>
  int(4096)
  ["End of file"]=>
  string(3) ":-)"
}
```

You'll notice that in the example provided for processing YAML data post-instantiation, the `process` method is used. You can use the return value of the `process` method to determine whether the input (the first parameter of the process method) is valid YAML data (to the extent supported by the class).

The `process` method supports four parameters:

```PHP
public function process(string $In, array &$Arr, int $Depth = 0, bool $Refs = false): bool;
```

The second parameter, generally, should always point to the `Data` member of the same object. The `Data` member is an array intended specifically for holding the processed YAML data. It's possible to point it elsewhere without causing problems, and pointing it elsewhere could be necessary for some implementations, but keeping all the object's own data self-contained is generally a cleaner, more recommended approach.

The third parameter should never be populated by the implementation. The process method can call itself recursively, and the third parameter is populated during such recursive calls by the method itself.

The fourth parameter is an optional boolean, false by default. When set to true, the array referenced by the second parameter will be referenced to the `Refs` member, which can be used as a data source for inline variables (similar to what Ansible can do with YAML). The `Refs` member can also be populated manually, or not at all, if preferred.

---


### Reconstruction.

__*Can the reverse be done, too? Can an array be converted into YAML data?*__

Yes. To do this, use the reconstruct method. The reconstruct method supports three parameters:

```PHP
public function reconstruct(array $Arr, bool $UseCaptured = false, bool $DoWithAnchors = false): string
```

The first parameter is the array that you want converted into YAML data. If you want to convert the object's own self-contained, already processed YAML data, just use the object's `Data` member as the reconstruct method's parameter.

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

```YAML
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

echo 'Is the reconstructed YAML data and the original YAML data the same? ';

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


Is the reconstructed YAML data and the original YAML data the same? Yes.
```

When the second parameter, `UseCaptured`, is set to `true`, the YAML data will be reconstructed using the comment headers and indent style captured previously by the `process` method. The parameter's default value is `false`.

When the third parameter, `DoWithAnchors`, is set to `true`, the method will *attempt* to reconstruct the anchors utilised by the YAML data. However, success requires that the instance be aware of the existence of those anchors, whether by reconstruction occurring via the same instance as that where the YAML data containing the necessary anchors has already been processed, or by manually populating the instance's internal anchors array. The parameter's default value is `false`.

The method returns a string (the reconstructed YAML data).

---


### Public class members.

```PHP
public $Data = [];
```

An array to contain all the data processed by the handler.

```PHP
public $Refs = [];
```

Used as a data source for inline variables.

```PHP
public $Indent = ' ';
```

Last indent used when processing YAML data.

```PHP
public $LastIndent = '';
```

Captured header comments from the YAML data.

```PHP
public $CapturedHeader = '';
```

Default indent to use when reconstructing YAML data.

```PHP
public $FoldedAt = 120;
```

Single line to folded multi-line string length limit.

```PHP
public $Anchors = [];
```

Used to cache any anchors found in the document.

```PHP
public $EscapeBySpec = false;
```

Indicates whether to escape according to the YAML specification.

#### Escaping.

When `EscapeBySpec` is set to `true`, escaping is performed in accordance with the YAML specification. For single-quoted strings (but *not* for double-quoted strings), apostrophes (`'`) are escaped with another apostrophe (`That's how it is -> That''s how it is`). For double-quoted strings (but *not* for single-quoted strings), all non-printable bytes, hashes (`#`), back-slashes (`\`), and extended characters (e.g., `\n`, `\r`, `\t`, etc) are escaped. For literals, folded lines, and any other kinds of quotes, no escaping is performed. Whether `EscapeBySpec` is set to `true` or `false`, escaping behaves the same way, with the exception that double-quotes (`"`) and back-slashes (`\`) are escaped when `EscapeBySpec` is set to `true` (for double-quoted strings), but not when `EscapeBySpec` is set to `false`.

#### Unescaping.

`EscapeBySpec` influences only escaping; it has no influence on unescaping. Unescaping is always performed in accordance with the YAML specification. For single-quoted strings (but *not* for double-quoted strings), apostrophes (`'`) are unescaped. For double-quoted strings (but *not* for single-quoted strings), any bytes escaped according to UTF-8 (`\x..`), UTF-16 (`\u....`), UTF-32 (`\U........`), etc are unescaped, and all extended characters (e.g., `\n`, `\r`, `\t`, etc) are unescaped. For literals, folded lines, and any other kinds of quotes, no unescaping is performed.

```PHP
public $Quotes = '"';
```

The preferred style of quotes to use for strings (double `"`, or single `'`) for when reconstructing YAML data.

```PHP
public $AllowedStringTagsPattern =
    '~^(?:addslashes|bin2hex|hex2bin|html(?:_entity_decode|entities|special' .
    'chars(?:_decode)?)|lcfirst|nl2br|ord|quotemeta|str(?:_rot13|_shuffle|i' .
    'p(?:_tags|c?slashes)|len|rev|tolower|toupper)|ucfirst|ucwords)$~';
```

The `coerce` method uses this regular expression to determine whether the tag specified matches the name of a string function that the YAML handler considers safe to use for manipulating the data in question. Tags matching the pattern will leverage the corresponding PHP function only if the applicable value is a string. The member is made public in order to allow the pattern to be modified when necessary, though care is recommended when doing so (e.g., allowing functions such as `eval` would likely introduce serious vulnerabilities to the implementation, so should never be allowed unless absolutely necessary).

```PHP
public $AllowedNumericTagsPattern =
    '~^(?:a(?:bs|cosh?|sinh?|tanh?)|ceil|chr|cosh?|dec(?:bin|hex|oct)|deg2r' .
    'ad|exp(?:m1)?|floor|log1[0p]|rad2deg|round|sinh?|tanh?|sqrt)$~';
```

The `coerce` method uses this regular expression to determine whether the tag specified matches the name of a numeric function that the YAML handler considers safe to use for manipulating the data in question. Tags matching the pattern will leverage the corresponding PHP function only if the applicable value is numeric (e.g., an integer, float, or number-like string). The member is made public in order to allow the pattern to be modified when necessary, though care is recommended when doing so.

---


### Supported data types.

The YAML handler supports arrays (or using YAML terminology, collections, mappings, sequences, etc), integers, floats, booleans, null, strings (single-quoted, double-quoted, etc), literal-style strings (`|`), folded-style strings (`>`), hexadecimal number notation (`0x`), binary number notation (`0b`), and octal number notation (`0o`).

The YAML handler does not (per PHP terminology) support callables, closures, or objects. If objects, closures, or callables are supplied to `reconstruct`, a fatal error will occur. Don't ever do this.

---


### Comments and implicit typing.

The YAML handler allows YAML data to contain comments. The YAML handler considers all data, beginning with a non-escaped hash (`#`), and ending at any valid line ending (e.g., `\n`, `\r`), to be a comment. Therefore, all hashes *not* intended to indicate the beginning of a comment should be properly escaped (i.e., `\#`), in order to ensure the YAML data is processed as intended.

The YAML handler implements implicit typing. Therefore, in order to avoid the "[Norway Problem](https://hitchdev.com/strictyaml/why/implicit-typing-removed/)", care should be taken to ensure that the appropriate quoting (or lack thereof) is used in order to obtain the appropriate data type.

When implicit typing is insufficient for obtaining the appropriate data type, YAML tags can be used as a means of explicit typing. However, due to the risk of confusion, the risk of users misunderstanding the intentions of the tags used, and due to that the YAML handler's `reconstruct` method doesn't reconstruct tags, abuse of tags should be avoided, and they should be used only when needed.

- When quoted (and assuming tags aren't used), the YAML handler will always resolve entries as strings.
- The YAML handler will always resolve literals and folded entries as strings.
- Within unprocessed YAML data, non-string data should never be quoted.
- As long as it doesn't cause ambiguity within implicit typing, quotes for strings remains optional, and won't generally matter too much (i.e., quotes for strings aren't strictly enforced). However, whenever there's risk of ambiguity, strings should always be quoted. For example, `Foo: "false"`, `Foo: "123"`, and `Foo: "12.3"` would all resolve to strings, whereas `Foo: false`, `Foo: 123`, and `Foo: 12.3` would resolve to a boolean (`false`), an integer, and a float respectively.
- Quoting for keys is treated in the same manner as quoting for values.

When reconstructing YAML data, the preferred quotes to use for string values (and for that matter, whether to use quotes at all) can be controlled via the `Quotes` public member. However, the YAML handler will never apply quotes to keys. Therefore, if you ever need to reverse some YAML data for any reason (i.e., process some YAML data, maybe make some modifications, and then reconstruct it back into YAML data again), you should always approach quoting strictly, should never quote keys, and should never use `true`, `false`, or `null` as names for keys (because unquoted, they'll look like booleans or null, and neither booleans nor null can be used as the names of array keys in PHP, meaning that you'll need to quote them to forcefully identify them as strings, but the reconstruct method would unquote them when reconstructing the data, causing an inconsistency between the original YAML data and the reconstruted YAML data). Worth noting too, that PHP resolves both `null` and `false` to empty strings when used as array keys.

---


### Anchors, aliases, and inline variables.

The YAML handler supports anchors and aliases.

Example anchor and alias usage:

```YAML
Anchored text push: &TestAnchor "Lorem ipsum."
Anchored text pull: *TestAnchor
```

Result:

```
array(2) {
  ["Anchored text push"]=>
  string(12) "Lorem ipsum."
  ["Anchored text pull"]=>
  string(12) "Lorem ipsum."
}
```

The YAML handler also inline variables (when correctly registered to the `Refs` member), which can be traversed via dot notation.

Example inline variable usage:

```YAML
Foo:
 Bar: "Baz"
Foz: "Hello there! My name is {{Foo.Bar}}! :-)"
```

Result:

```
array(2) {
  ["Foo"]=>
  array(1) {
    ["Bar"]=>
    string(3) "Baz"
  }
  ["Foz"]=>
  string(32) "Hello there! My name is Baz! :-)"
}
```

---


### Supported from the specification.

__[Tags](https://yaml.org/type/)__ | __Supported__
:--|:--
[`!!map`](https://yaml.org/type/map.html)<br />[`!!omap`](https://yaml.org/type/omap.html) | Both are supported (resolves to an associative array). However, because PHP arrays always have an "order" (i.e., a key index), I see no effective difference between `!!map` and `!!omap` in the context of a YAML handler written for PHP. Therefore, both tags resolve at the same block of code within the YAML handler and have the same effect. Also worth noting that keys must come from somewhere: If these tags are applied to a non-array or a non-associative array (therefore meaning there aren't any keys to start with), the effect will be automatically assigned keys (i.e., a numeric array; equivalent to `!!seq`). Therefore, practical applications of this tag in the context of the YAML handler are likely to be limited.
[`!!pairs`](https://yaml.org/type/pairs.html) | No. Because PHP doesn't allow arrays to have duplicate keys, and because any given singular scalar in PHP will have a singular value, using arrays or scalars to implement pairs in PHP doesn't make sense. True custom data types also aren't supported by PHP, so simply creating a new "pair" data type for PHP doesn't make sense either. The need for custom data types can be mostly abated, and the actual role and effect of custom data types fulfilled for the most part, by way of writing classes (or, if using the most recent versions of PHP, enums) to instantiate objects where applicable, so I'd briefly considered taking that approach (e.g., I could've created a simple class, which when instantiated, holds an arbitrary number of values, and utilises `__toString()` to invoke one of the held values whenever the object is used as a string, and an internal iterator or similar mechanism to shift the index for the held values to invoke a different value when the object is used as a string again at a later point). However, I felt that such an approach would diverge too much from other processors, would require users to have intimiate knowledge of the YAML handler to be able to use it properly, and would inevitably lock the affected documents down to the YAML handler quite closely, so I decided instead against trying to implement support for `!!pairs` at all.
[`!!set`](https://yaml.org/type/set.html) | Yes. Resolves to an array with all null values.
[`!!seq`](https://yaml.org/type/seq.html) | Yes. Resolves to a numeric array.
[`!!binary`](https://yaml.org/type/binary.html) | Yes. Uses PHP's `base64_decode()` function internally.
[`!!bool`](https://yaml.org/type/bool.html) | Yes.
[`!!float`](https://yaml.org/type/float.html) | Yes.
[`!!int`](https://yaml.org/type/int.html) | Yes.
[`!!merge`](https://yaml.org/type/merge.html) and the `<<` merge key. | Both are supported.
[`!!null`](https://yaml.org/type/null.html) | Yes.
[`!!str`](https://yaml.org/type/str.html) | Yes.
[`!!timestamp`](https://yaml.org/type/timestamp.html) | No. I'm not really sure what to do with this internally. Do I convert it to a unix timestamp (`int`)? Do I just leave it be as a string (in which case, it's redundant, because the expected input is already a string anyway)? Because I'm not sure, I'm ignoring it.
[`!!value`](https://yaml.org/type/value.html) | No. I'm not really sure what to do with this internally, so I'm ignoring it.
[`!!yaml`](https://yaml.org/type/yaml.html) | No. I'm not really sure what to do with this internally. Do I clone the YAML handler instance to the variable? Do I simply treat the data as YAML data (in which case, it's redundant, because the YAML handler is already doing that anyway)? Because I'm not sure, I'm ignoring it.
__Examples from [2.1. Collections](https://yaml.org/spec/1.2.2/#21-collections)__ | __Will using the YAML handler to process it produce the desired results?__
2.1 Sequence of Scalars | Yes.
2.2 Mapping Scalars to Scalars | Yes.
2.3 Mapping Scalars to Sequences | Yes.
2.4 Sequence of Mappings | Yes.
2.5 Sequence of Sequences | Yes.
2.6 Mapping of Mappings | The YAML handler doesn't yet support the particular flow context shown in that example, so no, not yet. But, I aim to fix that in the near future.
__Examples from [2.2. Structures](https://yaml.org/spec/1.2.2/#22-structures)__ | __Will using the YAML handler to process it produce the desired results?__
2.7 Two Documents in a Stream | *Kind of.* The YAML handler processes the YAML data supplied to it into to the specified PHP array. Because the YAML handler doesn't support "streams", it doesn't clearly distinguish between distinct documents. That PHP array will still be just a normal PHP array, no matter how many documents the supplied YAML data contains. So, in that sense, no; not supported. However, the YAML handler does recognise "start of document" (`---`) and "end of document" (`...`) markers, and will resolve those markers to the specified PHP array in such a way that, when reconstructing that array back into YAML data via the `reconstruct` method, it'll be correctly resolved back into those original "start of document" and "end of document" markers again, meaning that other processors subsequently working on that YAML data should still be able to distinguish between any/all distinct documents. So, in that sense, yes; supported.
2.8 Play by Play Feed from a Game | Same as above.
2.9 Single Document with Two Comments | Same as above.
2.10 Node for “`Sammy Sosa`” appears twice in this document | Same as above.
2.11 Mapping between Sequences | Nope. The YAML handler treats "complex mapping keys" as sequences of null values, so having key/value pairs immediately follow on from that, all within the same line, won't work as expected.
2.12 Compact Nested Mapping | Nope. The specification expects this to be processed in a similar way as a sequence of mappings would be processed. However, as the example shows key/value pairs attached to what looks like sequence indicators, followed by key/value pairs on the subsequent line without any such indicators, but with greater indentation so as to line them up with their earlier counterparts, to the YAML handler, the whole block just looks like a sequence, and those key/value pairs with greater indentation, due to that greater indentation, will cause the YAML handler to implicitly coerce their earlier counterparts to arrays so that those key/value pairs can be processed to there, thus loosing the values of those earlier counterparts. I understand the problem, and I may fix it in the future, but it's low priority on the to-do list and might require a significant amount of refactoring once I start, so I'm not entirely sure if or when.
__Examples from [2.3. Scalars](https://yaml.org/spec/1.2.2/#23-scalars)__ | __Will using the YAML handler to process it produce the desired results?__
2.13 In literals, newlines are preserved | It *would*. Except that, the YAML handler doesn't understand "`--- \|`" properly.<br />To produce the desired results, "`---: \|` would need to be used instead.
2.14 In the folded scalars, newlines become spaces | It *would*. Except that, the YAML handler doesn't understand "`--- >`" properly.<br />To produce the desired results, "`---: >` would need to be used instead.
2.15 Folded newlines are preserved for “more indented” and blank lines | Not yet. But, I aim to fix that in the near future.
2.16 Indentation determines scope | Yes.
2.17 Quoted Scalars | For everything other than the "not a comment" line. The YAML handler requires that all hashes be escaped in order to not be recognised as comments, and the hash in the example isn't escaped.
2.18 Multi-line Flow Scalars | Nope. Please use "`\|`" to do that with the YAML handler.
__[Character encodings](https://yaml.org/spec/1.2.2/#52-character-encodings)__ | __Supported__
UTF-32BE (Explicit BOM) | Yes.
UTF-32BE (ASCII first character) | Yes.
UTF-32LE (Explicit BOM) | Yes.
UTF-32LE (ASCII first character) | Yes.
UTF-16BE (Explicit BOM) | Yes.
UTF-16BE (ASCII first character) | Yes.
UTF-16LE (Explicit BOM) | Yes.
UTF-16LE (ASCII first character) | Yes.
UTF-8 (Explicit BOM) | Yes.
UTF-8 (Default) | Yes.
*"The recommended output encoding is UTF-8"* | The YAML handler uses UTF-8 as its default for everything, such as when reconstructing YAML data.

The overall specification is quite extensive, and writing this documentation takes a long time. I'll try to slowly document what I can, when I can, but it may take a while. If there's something missing from here that you particularly need listed ASAP, let me know, and I'll see what I can do.

---


### Additionally supported.

__Tags (specific methods implemented)__ | __Description__
:--|:--
`!flatten` | Flattens a multidimensional array down to a single depth (similar to merge, but rather than merging the array to the parent collection, it merges all the sub-arrays into the array being worked upon).
__Tags (directly invokes PHP functions at `coerce`)__ | __Description__
`!abs` | Uses PHP's `abs()` function to process the entry.
`!acos` | Uses PHP's `acos()` function to process the entry.
`!acosh` | Uses PHP's `acosh()` function to process the entry.
`!addslashes` | Uses PHP's `addslashes()` function to process the entry.
`!asin` | Uses PHP's `asin()` function to process the entry.
`!asinh` | Uses PHP's `asinh()` function to process the entry.
`!atan` | Uses PHP's `atan()` function to process the entry.
`!atanh` | Uses PHP's `atanh()` function to process the entry.
`!bin2hex` | Uses PHP's `bin2hex()` function to process the entry.
`!ceil` | Uses PHP's `ceil()` function to process the entry.
`!chr` | Uses PHP's `chr()` function to process the entry.
`!cos` | Uses PHP's `cos()` function to process the entry.
`!cosh` | Uses PHP's `cosh()` function to process the entry.
`!decbin` | Uses PHP's `decbin()` function to process the entry.
`!dechex` | Uses PHP's `dechex()` function to process the entry.
`!decoct` | Uses PHP's `decoct()` function to process the entry.
`!deg2rad` | Uses PHP's `deg2rad()` function to process the entry.
`!exp` | Uses PHP's `exp()` function to process the entry.
`!expm1` | Uses PHP's `expm1()` function to process the entry.
`!floor` | Uses PHP's `floor()` function to process the entry.
`!hex2bin` | Uses PHP's `hex2bin()` function to process the entry.
`!hash:*` | Uses PHP's `hash()` function to process the entry. Replace `*` with the desired algorithm (e.g., `!hash:md5`, `!hash:sha256`, etc). Supported algorithms determined by the returned value of `hash_algos()` (exact results may vary between systems and PHP versions).
`!html_entity_decode` | Uses PHP's `html_entity_decode()` function to process the entry.
`!htmlentities` | Uses PHP's `htmlentities()` function to process the entry.
`!htmlspecialchars_decode` | Uses PHP's `htmlspecialchars_decode()` function to process the entry.
`!htmlspecialchars` | Uses PHP's `htmlspecialchars()` function to process the entry.
`!lcfirst` | Uses PHP's `lcfirst()` function to process the entry.
`!log10` | Uses PHP's `log10()` function to process the entry.
`!log1p` | Uses PHP's `log1p()` function to process the entry.
`!nl2br` | Uses PHP's `nl2br()` function to process the entry.
`!ord` | Uses PHP's `ord()` function to process the entry.
`!quotemeta` | Uses PHP's `quotemeta()` function to process the entry.
`!rad2deg` | Uses PHP's `rad2deg()` function to process the entry.
`!round` | Uses PHP's `round()` function to process the entry.
`!sin` | Uses PHP's `sin()` function to process the entry.
`!sinh` | Uses PHP's `sinh()` function to process the entry.
`!sqrt` | Uses PHP's `sqrt()` function to process the entry.
`!str_rot13` | Uses PHP's `str_rot13()` function to process the entry.
`!str_shuffle` | Uses PHP's `str_shuffle()` function to process the entry.
`!strip_tags` | Uses PHP's `strip_tags()` function to process the entry.
`!stripcslashes` | Uses PHP's `stripcslashes()` function to process the entry.
`!stripslashes` | Uses PHP's `stripslashes()` function to process the entry.
`!strlen` | Uses PHP's `strlen()` function to process the entry.
`!strrev` | Uses PHP's `strrev()` function to process the entry.
`!strtolower` | Uses PHP's `strtolower()` function to process the entry.
`!strtoupper` | Uses PHP's `strtoupper()` function to process the entry.
`!tan` | Uses PHP's `tan()` function to process the entry.
`!tanh` | Uses PHP's `tanh()` function to process the entry.
`!ucfirst` | Uses PHP's `ucfirst()` function to process the entry.
`!ucwords` | Uses PHP's `ucwords()` function to process the entry.


---


Last Updated: 13 February 2022 (2022.02.13).
