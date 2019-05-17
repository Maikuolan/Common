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

---


Last Updated: 17 May 2019 (2019.05.17).
