### Documentation for the "LazyArray" class.

*For cases where it might be necessary to process some data in some way to generate an array, where that array mightn't always be needed for every request, but where the process to generate it must always be defined at some specific point in the code which is to be always executed, LazyArray provides a means by which the process can be defined while deferring its actual execution until either the array is actually accessed where needed (i.e., the object is treated as an array) or the execution is triggered manually, which could potentially improve performance at the implementation in some cases by reducing unnecessary processing.*

---


### How to use:

When instantiating the class, the constructor accepts two parameters. The first parameter is a closure responsible for processing the data (i.e., the processor to be triggered when the object is treated as an array, or when triggered manually). The second parameter is the raw data to be worked on by that closure.

A very simple example:

```PHP
<?php
$Object = new \Maikuolan\Common\LazyArray(function ($Input) {
    return explode(',', $Input);
}, 'foo,bar,baz');

// Prints "baz".
echo $Object[2];
```

Or:

```PHP
<?php
$Object = new \Maikuolan\Common\LazyArray(function ($Input) {
    return explode(',', $Input);
}, 'foo,bar,baz');

$Object->trigger();

// Prints
// array(3) {
//   [0]=>
//   string(3) "foo"
//   [1]=>
//   string(3) "bar"
//   [2]=>
//   string(3) "baz"
// }
var_dump($Object->Data);
```

The only public property is "Data", which at instantiation, is an empty array. When the object is accessed in an array-like way, and the closure consequentially processes the raw data to generate an array, that generated array will be populated to that property, and that property will be leveraged when accessing the object is an array-like way. Therefore, after the raw data has been processed, there's effectively no difference between `$SomeVar = $Obj->Data['Foo'];` and `$SomeVar = $Obj['Foo'];`, or between `$Obj->Data['Foo'] = $SomeVar;` and `$Obj['Foo'] = $SomeVar;`. However, accessing that property in an object-like way (e.g., `$SomeVar = $Obj->Data['Foo'];` or `$Obj->Data['Foo'] = $SomeVar;`) won't trigger the closure (to trigger the closure without accessing the object in an array-like way, calling the `trigger()` method would be necessary).

```PHP
public $Data = [];
```

---


Last Updated: 17 March 2026 (2026.03.17).
