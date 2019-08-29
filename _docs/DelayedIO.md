### Documentation for the "DelayedIO" class.

*Provides an easy, simple solution for when needing to read and update a number of files, but delay rewriting the files for a while.*

---


### Use-case:

You need to get the contents of some files. Maybe you do something like this:

```PHP
$Data = file('some_file.txt');
```

Or something like this:

```PHP
$Handle = fopen('some_file.txt', 'rb');
$Data = fread($Handle, filesize('some_file.txt'));
fclose($Handle);
```

Maybe you need to perform some batch operations on these files. So, you create some kind of iterator or loop to iterate through them all:

```PHP
$Files = ['file1.txt', 'file2.txt', 'file3.txt', 'file4.txt', 'file5.txt'];
foreach ($Files as $File) {

    // Read the file.
    $Handle = fopen('some_file.txt', 'rb');
    $Data = fread($Handle, filesize('some_file.txt'));
    fclose($Handle);

    // Do something.
    $Data = do_something($Data);

    // Write the file.
    $Handle = fopen('some_file.txt', 'wb');
    fwrite($Handle, $Data);
    fclose($Handle);
}
```

So far, so good. But, what if the elements of "$Files" isn't predictable? What if there are some repeated elements (i.e., the same file is queued multiple times for the batch operation in question)? There are some undesirable possibilities here:

1. An element appears that corresponds to a file that doesn't exist, or which isn't readable, causing `fopen()` to raise an error, and possibly `do_something()` to raise additional errors (depending on whatever it does).
2. Maybe the batch operation isn't really necessary for some particular file, and the file's associated data remains unchanged after being processed by `do_something()`. In this case, rewriting the file isn't necessary, so, we end up with an unnecessary IO operation (when we rewrite the file).
3. If we process repeated elements, we could end up needlessly repeating the entire process multiple times for the same files (more unnecessary IO operations).

Problem 1 can be resolved by implementing a simple `is_file()` check and other similar safeguards:

```PHP
$Files = ['file1.txt', 'file2.txt', 'file3.txt', 'file4.txt', 'file5.txt', 'file2.txt'];
foreach ($Files as $File) {

    // Guard.
    if (!is_file($File) || !is_readable($File)) {
        continue;
    }

    // Read the file.
    $Handle = fopen('some_file.txt', 'rb');
    $Data = fread($Handle, filesize('some_file.txt'));
    fclose($Handle);

    // Do something.
    $Data = do_something($Data);

    // Write the file.
    $Handle = fopen('some_file.txt', 'wb');
    fwrite($Handle, $Data);
    fclose($Handle);
}
```

Problem 2 is a little too complicated for simple examples like those given by this document, in the sense that determining what is "necessary" will depend on the specific requirements of the implementation, and therefore, the appropriate solution will differ from one implementation to another. However, doing something like:

```PHP
if ($NewData === $OldData) {
    continue;
}
```

..to guard against needless rewrite operations just prior to the second `fopen()` should help with this.

Problem 3 can be easily resolved by implementing `array_unique()`:

```PHP
$Files = ['file1.txt', 'file2.txt', 'file3.txt', 'file4.txt', 'file5.txt', 'file2.txt'];
$Files = array_unique($Files); // Will strip the final "file2.txt" from the array (because it's a duplicate).
foreach ($Files as $File) {

    // Guard.
    if (!is_file($File) || !is_readable($File)) {
        continue;
    }

    // Read the file.
    $Handle = fopen('some_file.txt', 'rb');
    $Data = fread($Handle, filesize('some_file.txt'));
    fclose($Handle);

    // Do something.
    $Data = do_something($Data);

    // Write the file.
    $Handle = fopen('some_file.txt', 'wb');
    fwrite($Handle, $Data);
    fclose($Handle);
}
```

*Okay then.. So why do we need this class again?*

What happens if we're parsing possibly either a different set of files, or the same set of files (maybe we're not sure which), through multiple loops, each which deals with a different set of processes, to satisfy a range of different requirements of the implementation? In short: Your code starts becoming complicated (in terms of which files are being read/modified, exactly where in the codebase this is happening, and for what purpose, how many times, etc) and maintainability is ultimately reduced.

What this class does is actually super simple: It provides some methods that act as simple wrappers for `fopen()`, `fread()`, `fwrite()`, etc that you can use to read from or write to a file, but with the aforementioned safeguards implemented into these methods (so that you don't need to worry about implementing them yourself), some additional checks that ensure that files are only rewritten when the data associated with those files has actually been changed (so, files that aren't changed, aren't rewritten), and takes advantage of PHP's support for `__destruct` magic methods to delay rewriting the files until the object (i.e., the class instance) is destroyed. That basically means that files won't be needlessly rewritten multiple times per instance (so long as each delayed rewrite operation is sent to the same object instance, seeing as different instances of the class aren't mutually aware of each other), thus making it easy to avoid some of the spaghetti-esque legwork that might potentially plague the implementation in such situations otherwise.

As an example, imagine this scenario:

```PHP
// Instantiate the object.
$FileIO = new \Maikuolan\Common\DelayedIO();

// Arbitrary list of files (some files are repeated, but each file will nonetheless be rewritten only once).
$Files = ['file1.txt', 'file1.txt', 'file2.txt', 'file2.txt', 'file2.txt', 'file3.txt'];

foreach ($Files as $File) {
    // Read the file.
    $Data = $FileIO->readFile($File);

    // Do something.
    $Data = do_something($Data);

    // Write the file.
    $Data = $FileIO->writeFile($File, $Data);
}

// Destroy the object, committing all file changes and releasing the associated data.
unset($FileIO);
```

If we were to duplicate that `foreach()` some several times, to perform several different operations, maybe parsing it through other, unrelated classes or similar, then, utilising this class could prove a little cleaner and simpler than the alternative, wouldn't you agree? :-)

---


### How to use:

- [readFile method.](#generatemarkers-method)
- [writeFile method.](#iterateclosure-method)

#### readFile method.

The readFile method is used to read files.

```PHP
public function readFile(string $File = '', int $Lock = 0): string
```

It accepts two parameters. The first parameter is the path to the file to be read, and the second parameter (optional) should be treated in the same manner as the second parameter for PHP's `flock()` function, providing the ability to indicate whether the operation should attempt to lock the file when reading it.

Example:

```PHP
$FileIO = new \Maikuolan\Common\DelayedIO();
$File = 'hello.txt';
$Data = $FileIO->readFile($File, LOCK_SH);
```

The method returns the contents of the file, or an empty string on failure. If it's indicated to the method that it should attempt to lock the file when reading it, and it subsequently fails to acquire a lock, the operation is regarded as a failure (therefore returning an empty string).

#### writeFile method.

The writeFile method is used to queue file rewrite operations.

```PHP
public function writeFile(string $File = '', string $Data = '', int $Lock = 0): bool
```

It accepts three parameters. The first parameter is the path to the file to be written, the second parameter (optional) is the data to be written to the file in question, and the third parameter (optional) should be treated in the same manner as the second parameter for PHP's `flock()` function, providing the ability to indicate whether the operation should attempt to lock the file when later rewriting it.

Example:

```PHP
$FileIO = new \Maikuolan\Common\DelayedIO();
$File = 'hello.txt';
$Data = $FileIO->writeFile($File, 'Hello World', LOCK_EX);
```

The method returns true when the target file is writable, or false otherwise. When the target file isn't writable, nothing is queued (so, no rewrite operation will be attempted for the file in question when the object is destroyed, unless a subsequent call to the method is successful in this regard).

In order to be able to compare the file's original data with the data associated with the queued rewrite operation, `readFile()` must've been called for the file in question prior to calling `writeFile()` for the file in question. When that has been satisfied, the rewrite operation for the file in question won't occur when its original data and its new data is the same. When that hasn't been satisfied, the rewrite operation will always occur regardless, as long as the new data isn't empty (if not satisfied, and the new data is also empty, the rewrite operation won't occur).

---


Last Updated: 26 August 2019 (2019.08.26).
