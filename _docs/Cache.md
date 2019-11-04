### Documentation for the "Cache" class.

*A simple, unified cache handler used by CIDRAM and phpMussel for their caching needs. Currently, it supports APCu, Memcached, Redis, PDO, and flatfile caching.*

---


### How to use:

Let's start with an example. CIDRAM leverages the Cache class via the `InitialiseCache` closure in CIDRAM's main function file. *Excerpt:*

```PHP
    /** Create new cache object. */
    $CIDRAM['Cache'] = new \Maikuolan\Common\Cache();
    $CIDRAM['Cache']->EnableAPCu = $CIDRAM['Config']['supplementary_cache_options']['enable_apcu'];
    $CIDRAM['Cache']->EnableMemcached = $CIDRAM['Config']['supplementary_cache_options']['enable_memcached'];
    $CIDRAM['Cache']->EnableRedis = $CIDRAM['Config']['supplementary_cache_options']['enable_redis'];
    $CIDRAM['Cache']->EnablePDO = $CIDRAM['Config']['supplementary_cache_options']['enable_pdo'];
    $CIDRAM['Cache']->MemcachedHost = $CIDRAM['Config']['supplementary_cache_options']['memcached_host'];
    $CIDRAM['Cache']->MemcachedPort = $CIDRAM['Config']['supplementary_cache_options']['memcached_port'];
    $CIDRAM['Cache']->RedisHost = $CIDRAM['Config']['supplementary_cache_options']['redis_host'];
    $CIDRAM['Cache']->RedisPort = $CIDRAM['Config']['supplementary_cache_options']['redis_port'];
    $CIDRAM['Cache']->RedisTimeout = $CIDRAM['Config']['supplementary_cache_options']['redis_timeout'];
    $CIDRAM['Cache']->PDOdsn = $CIDRAM['Config']['supplementary_cache_options']['pdo_dsn'];
    $CIDRAM['Cache']->PDOusername = $CIDRAM['Config']['supplementary_cache_options']['pdo_username'];
    $CIDRAM['Cache']->PDOpassword = $CIDRAM['Config']['supplementary_cache_options']['pdo_password'];
    $CIDRAM['Cache']->FFDefault = $CIDRAM['Vault'] . 'cache.dat';

    if (!$CIDRAM['Cache']->connect()) {
        if ($CIDRAM['Cache']->Using === 'FF') {
            header('Content-Type: text/plain');
            die('[CIDRAM] ' . $CIDRAM['L10N']->getString('Error_WriteCache'));
        } else {
            $Status = $CIDRAM['GetStatusHTTP'](503);
            header('HTTP/1.0 503 ' . $Status);
            header('HTTP/1.1 503 ' . $Status);
            header('Status: 503 ' . $Status);
            header('Retry-After: 3600');
            die;
        }
    }
```

The above example can be broken down into four main parts:

#### 1. Instantiation.

Before we can do anything else with the Cache class, an instance of it must be created.

```PHP
$Instance = new \Maikuolan\Common\Cache();
```

Note: If you want the instance to store and fetch cache items internally, within itself only, instead of storing and fetching cache items using any of the various supported caching mechanisms (i.e., instead of using APCu, Memcached, Redis, PDO, flatfile caching, etc), you can supply an array of cache items as the sole parameter of the constructor during instantiation. Otherwise (and in most cases generally), no parameters should be supplied to the constructor during instantiation.

#### 2. Configuration.

After creating an instance of Cache, before leveraging any of its methods, we should configure it. The excerpt below is provided as an example (the actual values used in the excerpt are the default values for the instance members the values are being assigned to, and therefore have no effect in this particular example; they're optional too, and so, any that aren't likely to ever be needed by the implementation can effectively be omitted and ignored).

```PHP
$Instance->EnableAPCu = false; // Boolean (whether to try using APCu).
$Instance->EnableMemcached = false; // Boolean (whether to try using Memcached).
$Instance->EnableRedis = false; // Boolean (whether to try using Redis).
$Instance->EnablePDO = false; // Boolean (whether to try using PDO).
$Instance->MemcachedHost = 'localhost'; // String (the host for Memcached to try using).
$Instance->MemcachedPort = 11211;  // Integer (the port for Memcached to try using).
$Instance->RedisHost = 'localhost'; // String (the host for Redis to try using).
$Instance->RedisPort = 6379;  // Integer (the port for Redis to try using).
$Instance->RedisTimeout = 2.5; // Float or integer (the timeout for Redis to try using).
$Instance->PDOdsn = ''; // String (the DSN to use for PDO connections).
$Instance->PDOusername = ''; // String (the username to use for PDO connections).
$Instance->PDOpassword = ''; // String (the password to use for PDO connections).
$Instance->FFDefault = ''; // String (the path to a flatfile to use for caching).
```

The correct values to use, and the best way to configure the instance, depends on which caching mechanisms you want to use, whether those caching mechanisms are available in your environment, and how those caching mechanisms themselves are configured (e.g., the correct host and port number to use might be different than the default for your particular environment, and if so, you'll need to determine that information for yourself).

I would, in most cases, recommend defining `FFDefault`, regardless of your chosen caching mechanism, as a fallback, in case your chosen caching mechanism isn't available, either temporarily or permanently, at some point in the future, for whatever reason. When defining `FFDefault`, you should make sure that the path is actually writable (setting an unwritable path means that nothing gets written, and is thus pointless).

Beyond that, I would recommend defining values only for the members that relate to caching mechanisms that you already know are available, and that you could foreseeably utilise for your implementation, omitting definitions for the members that relate to anything that you know to be unavailable or otherwise unsuitable for your implementation.

If all of the supported caching mechanisms are available in your environment, and you're having difficulty deciding which to use, I would generally recommend APCu above the others, due to its simplicity: The only member you would need to define is `EnableAPCu` (no need to mess around with hosts, ports, etc), and in most cases, it should immediately start working. Conversely, utilising PDO is likely to be slightly more complicated than the other available choices, due to the need to set up an external database somewhere for it to interface with, the need to define a DSN (so that the instance can instruct PDO how to interface with your external database), etc.

#### 3. Connection.

After creating an instance of Cache and configuring it, before leveraging any of its methods, we need to connect to our chosen caching mechanism. We do this using the `connect` method. When the instance successfully connects, the `connect` method returns `true`. When the instance fails to connect (or when any other known problem occurs while trying to connect), the `connect` method returns `false`. When using flatfiles, the `connect` method checks whether the specified path to use for flatfile caching is valid, and if it's valid, attempts to read the flatfile cache into the instance (an invalid path, or failure to read the flatfile cache, is considered a failure). When multiple caching mechanisms are enabled for the instance, the instance will attempt to connect to the enabled caching mechanisms sequentially, until it successfully connects to one of them, returning `true` when it successfully connects to one of them, or returning `false` only after failing to connect to any of the enabled caching mechanisms. If none of the supported caching mechanisms are enabled, the `connect` method simply returns `true` if working data is already available, or `false` otherwise (generally, whether an array of cache items was supplied to the constructor during instantiation).

```PHP
$Connected = $Instance->connect(); // Boolean.
```

The `Using` member is populated by the `connect` method, and describes which caching mechanism `connect` was able to successfully connect to (i.e., which caching mechanism the instance should be "using" for any subsequent calls to any instance methods, e.g., to get or set cache items). The `Using` member is exposed as public, in case it could be useful to the implementation, but shouldn't be tampered with, because it's needed by most class methods to function correctly. *Possible values: "APCu", "Memcached", "Redis", "PDO", "FF" for flatfile caching, or an empty string when not using any supported caching mechanism, using just an internal array of cache items instead.*

```PHP
$Using = $Instance->Using(); // String.
```

#### 4. Handling failures.

In most cases, if an implementation implements a caching solution, it does so because doing so is necessary for correct functionality of the implementation, and it therefore won't be desirable in most cases for the implementation to continue execution when its implemented caching solution fails. In the case of implementing this particular class, the need to handle failure arises when the `connect` method returns `false` (indicating failure).

In the earlier above example, CIDRAM does this by printing the message to the end-user, "unable to write to the cache" (`$CIDRAM['L10N']->getString('Error_WriteCache')`), when flatfile caching is used, or by sending `503 Service Unavailable` headers when anything else is used, and then terminating the request.

### What next?

Now that you've configured and connected to an instance of Cache, you can create new cache items using `setEntry`, fetch existing cache items using `getEntry`, or use any of the other public methods provided by Cache for working with the instance.

Note that the destructor is responsible for closing any connections opened by `connect` and for writing any files that might need to be written (e.g., when using flatfile caching). If the destructor fails to execute (e.g., if PHP crashes, is forcibly terminated, or if the instance is never properly destroyed for whatever reason), connections might outlive the request, causing one or more extensions to crash upon subsequent requests, or files might not be written, causing cache data to be lost. If you want to want to close any connections opened by `connect`, and write any files that might need to be written, prior to the completion of the request, or at any particular, specific point in your code, you can do so by simply destroying the instance (thus executing the destructor).

All public methods provided by Cache, along with relevant instructions, are listed below.

- [__construct method.](#__construct-method)
- [__destruct method.](#__destruct-method)
- [connect method.](#connect-method)
- [checkTablesPDO method.](#checktablespdo-method)
- [getEntry method.](#getentry-method)
- [setEntry method.](#setentry-method)
- [deleteEntry method.](#deleteentry-method)
- [clearCache method.](#clearcache-method)
- [getAllEntries method.](#getallentries-method)
- [clearExpired method.](#clearexpired-method)
- [clearExpiredPDO method.](#clearexpiredpdo-method)
- [unserializeEntry method.](#unserializeentry-method)
- [serializeEntry method.](#serializeentry-method)
- [stripObjects method.](#stripobjects-method)
- [exposeWorkingDataArray method.](#exposeworkingdataarray-method)

#### __construct method.

The class constructor.

```PHP
public function __construct(array $WorkingData = null);
```

#### __destruct method.

The class destructor.

```PHP
public function __destruct();
```

#### connect method.

Connects the instance to a caching mechanism per the instance configuration (examples provided earlier in the documentation). Doesn't accept any parameters.

```PHP
public function connect(): bool;
```

#### checkTablesPDO method.

Checks whether a table exists for the instance to use and automatically creates it if it doesn't yet exist. Invoked automatically by the connect method when using PDO. Only applies to PDO and should never be called when not using PDO. Doesn't accept any parameters.

```PHP
public function checkTablesPDO(): bool;
```

It should be noted that this method hasn't been extensively tested against *every* database driver available to PDO, and therefore possibly may need to be refined/refactored/etc in the future, pending further research, testing and so on.

#### getEntry method.

Returns an entry from the cache, or false on failure. Accepts the name of the cache entry as its sole parameter.

```PHP
public function getEntry(string $Entry);
```

#### setEntry method.

Writes an entry to the cache, returning true on success, or false on failure. `$Key` is the name to assign to the cache entry. `$Value` is the value of the cache entry. `$TTL` ("time to live") is the number of seconds that the cache entry should persist (after which, the cache entry should be deleted).

```PHP
public function setEntry(string $Key, $Value, int $TTL = 3600): bool;
```

#### deleteEntry method.

Deletes an entry from the cache, returning true on success, or false on failure (this could be either a hard failure, caused by some unknown problem with the cache mechanism being used, or could simply be that the cache entry doesn't exist, because it was already deleted earlier, or never existed at all). `$Entry` is the name of the cache entry to delete.

```PHP
public function deleteEntry(string $Entry): bool;
```

#### clearCache method.

Deletes all entries from the cache, returning true on success, or false on failure. Doesn't accept any parameters.

```PHP
public function clearCache(): bool;
```

#### getAllEntries method.

Returns an associative array containing all entries from the cache (the array will be empty when no entries can be retrieved). Doesn't accept any parameters.

```PHP
public function getAllEntries(): array;
```

#### clearExpired method.

Deletes all expired cache entries from an array of cache entries, supplied by reference as the method's sole parameter. Returns true when one or more entries are deleted (meaning that the size of the referenced array should be reduced as a result of using the method), or false when nothing is deleted (meaning that the referenced array should remain unchanged as a result of using the method).

There are some other methods that rely on this method, but note that this method itself doesn't rely on any other methods, and also isn't directly tied to your choice of caching mechanism, nor requires that you connect to anything. Note that this method is also called by the destructor (and thus will be called when the instance is destroyed anyway). In most cases, you won't need to call it specifically from your implementation, but it is exposed as public nonetheless, just in case you need to call it specifically from your implementation for whatever reason (e.g., to forcibly delete expired cache entries prior to destroying the instance).

```PHP
public function clearExpired(array &$Data): bool;
```

#### clearExpiredPDO method.

Deletes all expired cache entries stored using PDO. Doesn't accept any parameters. Returns true when one or more entries are deleted, or false when nothing is deleted (false could also be returned when calling `clearExpiredPDO` from an instance that hasn't connected using PDO).

Note that this method won't be useful unless you're using PDO. Note that this method is also called by the destructor (and thus will be called when the instance is destroyed anyway). In most cases, you won't need to call it specifically from your implementation, but it is exposed as public nonetheless, just in case you need to call it specifically from your implementation for whatever reason (e.g., to forcibly delete expired cache entries prior to destroying the instance).

```PHP
public function clearExpiredPDO(): bool;
```

#### unserializeEntry method.

Used by various other methods to unserialize a returned cache entry. Accepts a serialized cache entry as its sole parameter.

In most cases, you won't need to call it specifically from your implementation, but it is exposed as public nonetheless, just in case you need to call it specifically from your implementation for whatever reason (e.g., for testing purposes).

```PHP
public function unserializeEntry($Entry);
```

#### serializeEntry method.

Used by various other methods to serialize a cache entry prior to committing it. Accepts the value of the cache entry as its sole parameter.

In most cases, you won't need to call it specifically from your implementation, but it is exposed as public nonetheless, just in case you need to call it specifically from your implementation for whatever reason (e.g., for testing purposes).

```PHP
public function serializeEntry($Entry);
```

#### stripObjects method.

Attempts to strip objects from a data set (could be useful as a security precaution to be used prior to writing cache entries, if an implementation is forced to accept data from untrusted sources for whatever reason). The data set can be of any data type, and the data set (sans objects) will be returned by the method. The method isn't used by any other methods in the class, and in most cases, will most likely never be needed, but is provided nonetheless, in case the implementation needs such a thing (it is thus the responsibility of the implementation to call the method if and when it becomes necessary). However, accepting data from untrusted sources is extremely inadvisable, and every reasonable measure should be taken to avoid it.

```PHP
public function stripObjects($Data);
```

The reason that you might want to strip objects from a data set prior to caching it (and by extension, the reason that you shouldn't be caching data from untrusted sources), is that cache entries are serialized when committed, unserialized when fetched, and magic methods (e.g., `__construct`, `__destruct`, etc) containing arbitrary, possibly dangerous code can be executed when PHP unserializes any objects containing such magic methods. Likewise, you shouldn't use opt to use any caching mechanisms shared with unknown or untrusted third-parties, in case those third-parties store such dangerous payloads in the cache, which the instance could thus faithfully fetch and unwittingly execute (ideally, your implementation alone should have sole access to your chosen caching mechanism).

#### exposeWorkingDataArray method.

Used to expose the instance's working data array. This can be useful when integrating the instance with external caching mechanisms that the class doesn't natively support. The method is also used internally by the `getAllEntries` method when the instance uses flatfile caching or isn't otherwise configured to use any specific supported caching mechanism. Doesn't accept any parameters. Returns the working data array, or false on failure (e.g., if the working data array doesn't exist).

```PHP
public function exposeWorkingDataArray();
```


---


Last Updated: 4 November 2019 (2019.11.04).
