### Documentation for the "Request" class.

*Used by CIDRAM and phpMussel to send outbound requests through cURL.*

---


### How to use:

- [DefaultTimeout property.](#defaulttimeout-property)
- [Channels property.](#channels-property)
- [Disabled property.](#disabled-property)
- [SendToOut property.](#sendtoout-property)
- [ObjLogger property.](#objlogger-property)
- [ObjLoggerFile property.](#objloggerfile-property)
- [Proxy property.](#proxy-property)
- [ProxyAuth property.](#proxyauth-property)
- [UserAgent property.](#useragent-property)
- [MostRecentStatusCode property.](#mostrecentstatuscode-property)
- [TryUsing property.](#tryusing-property)
- [DF property.](#df-property)
- [Supported property.](#supported-property)
- [STREAM_BLOCKSIZE constant.](#stream_blocksize-constantproperty)
- [request method.](#request-method)
- [inCsv method.](#incsv-method)
- [sendMessage method.](#sendmessage-method)
- [getCertPath method.](#getcertpath-method)

#### DefaultTimeout property.

Sets the default timeout to use for any requests which don't specify their own timeout.

```PHP
public $DefaultTimeout = 12;
```

#### Channels property.

Can be used to specify alternative channels to use for requests matching specific patterns.

```PHP
public $Channels = ['Triggers' => []];
```

*Example data (YAML):*
```YAML
Triggers:
 GitHub: "https://raw.githubusercontent.com/"
 BitBucket: "https://bitbucket.org/"
GitHub:
 X example:
  GitHub: "https://raw.githubusercontent.com/foo/bar/x/"
  BitBucket: "https://bitbucket.org/foo/bar/raw/x/"
  NotABug: "https://notabug.org/foo/bar/raw/x/"
 Y example:
  GitHub: "https://raw.githubusercontent.com/foo/bar/y/"
  BitBucket: "https://bitbucket.org/foo/bar/raw/y/"
  NotABug: "https://notabug.org/foo/bar/raw/y/"
 Hello world:
  GitHub: "https://raw.githubusercontent.com/hello/world"
  BitBucket: "https://bitbucket.org/hello/world/raw"
  NotABug: "https://notabug.org/hello/world/raw"
BitBucket:
 Lorem ipsum:
  BitBucket: "https://bitbucket.org/lorem/ipsum/raw"
  GitHub: "https://raw.githubusercontent.com/lorem/ipsum"
  NotABug: "https://notabug.org/lorem/ipsum/raw"
```

"Channels" is an array, containing at least one sub-array, "Trigger". Stored in that sub-array, each "pattern" matches against the beginning of the URL of the request, serving as a "trigger" for identifying alternative channels. Each "trigger" should have its own corresponding sub-array, containing any number of groups of potential sub-matches, each containing any potential alternative channels. The provided "alternative channels" will replace the part of the URL of the request which matches the corresponding sub-match, and a subsequent new request will be made using the amended URL.

You can also just ignore this property entirely if you don't want to utilise alternative channels at your implementation.

#### Disabled property.

A CSV listing any alternative channels that should be disabled for the request (useful, for example, if you have a static list of alternative channels for your implementation, but provide the ability for end-users to optionally disable channels of their choice).

```PHP
public $Disabled = '';
```

*Example (per the earlier provided example):*
```
X example,Y example,Hello world,Lorem ipsum
```

#### SendToOut property.

Whether to send the results of outbound requests to stdout (useful for debugging, but most likely won't ever be needed in production).

```PHP
public $SendToOut = false;
```

#### ObjLogger property.

Object-level logger for the results of outbound requests (useful for debugging potential problems with outbound requests at the implementation).

```PHP
public $ObjLogger = '';
```

#### ObjLoggerFile property.

Whether to dump the object-level logger to a file (and where to find it).

```PHP
public $ObjLoggerFile = '';
```

#### Proxy property.

The URL of a proxy to use if required by the instance.

```PHP
public $Proxy = '';
```

#### ProxyAuth property.

The username and password to use if required by the specified proxy URL.

```PHP
#[Context(Sensitive: true)]
public $ProxyAuth = '';
```

#### UserAgent property.

The default user agent to cite when sending requests (for the sake of good netiquette and politeness towards any endpoints you intend to communicate with, this should definitely be populated when implementing the class according to your implementation).

```PHP
public $UserAgent = 'Request class (https://github.com/Maikuolan/Common)';
```

#### MostRecentStatusCode property.

Whenever a request is successfully performed, the status code returned by that request will be populated to this property (e.g., 200, 403, 404, etc).

```PHP
public $MostRecentStatusCode = 0;
```

#### TryUsing property.

Automatically set to a recommended value during instantiation based on the functionality available, but can be manually set by the implementation if so desired.

When set to `1` (the default and preferred value), the request method will use curl to send requests. When set to `2` (intended for when curl isn't available at the implementation, e.g., if the curl PHP extension isn't enabled), the request method will use `fopen` with `stream_context_create` to send requests (i.e., as an HTTP/S wrapper/handler). When set to `0` (i.e., when neither curl nor the ability to use `fopen` with `stream_context_create` to send requests is available, e.g., because those functions are disabled, `open_basedir` is populated, and/or `allow_url_fopen` is set to `Off`), the request method will exit immediately upon being called/invoked, returning an empty string and not attempting to send the request. Setting any other value, for now, will have the same effect as setting to `1`, but shouldn't be relied upon in case those other values end up being used for additional functionality added to the class in the future (although such additional functionality isn't planned at this time).

```PHP
public $TryUsing = 1;
```

#### DF property.

A private property populated during instantiation, used internally to check whether specific PHP functions are disabled.

```PHP
private $DF = [];
```

#### Supported property.

A private property populated during instantiation, used internally to check the protocol specified for any given request against which protocols are supported by the class.

```PHP
private $Supported = [1 => [], 2 => ['ftp' => 1, 'ftps' => 1, 'http' => 1, 'https' => 1]];
```

Explicitly supported when using curl (when `$this->TryUsing` is set to `1`):
- FTP
- FTPS
- HTTP
- HTTPS
- SFTP
- TFTP

Others *potentially may* work if your curl installation is configured accordingly, but as the class hasn't been coded specifically with others in mind, aren't guaranteed to work, and most likely won't.

Supported when using `fopen` with `stream_context_create` (when `$this->TryUsing` is set to `2`):
- FTP
- FTPS
- HTTP
- HTTPS

#### STREAM_BLOCKSIZE constant.

A private constant representing how many bytes to read at a time when reading the response to a request sent using `fopen` with `stream_context_create`.

```PHP
private const STREAM_BLOCKSIZE = 131072;
```

#### request method.

The main request method (this is what you'll want to use to actually perform a request).

```PHP
public function request(string $URI, $Params = [], int $Timeout = -1, array $Headers = [], int $Depth = 0, string $Method = ''): string;
```

The first parameter (`$URI`) is the URL, URI, resource, etc that you want to request.

The second parameter (`$Params`) is an optional, associative array of key-value pairs for any post fields you may want to send along with your request.

When sending an HTTP/S request: If empty or omitted, `CURLOPT_POST` is `false`. Otherwise, `CURLOPT_POST` is true. The post fields will be populated to `CURLOPT_POSTFIELDS`.

When sending an FTP/S request: If sending the request to a server requiring a username and password, include an element with the key `USERPWD`, the value containing the required username and password as `Username:Password`. That element will be populated to `CURLOPT_USERPWD`.

The third parameter (`$Timeout`) is an optional timeout limit for the request. When omitted, `DefaultTimeout` is used instead.

The fourth parameter (`$Headers`) is an optional array of headers to send with the request.

The fifth parameter (`$Depth`) represents the recursion depth of the current request instance, is populated automatically by `request`, and shouldn't be populated manually by the implementation (other than when needing access to the sixth parameter).

The sixth parameter (`$Method`) can be used to specify the intended request method in the event that the method intended isn't GET or POST. When the intended request method is GET or POST, it shouldn't be populated manually by the implementation (the method will determine automatically whether GET or POST is needed, based on factors like request parameters). This can be useful when methods such as CONNECT or DELETE are needed.

The method returns a string (the response to the request if successful, or an empty string on failure).

The class also implements the magic method `__invoke`, as a way to alias back to `request` when the instance is utilised as a callable or function.

```PHP
public function __invoke(...$Params): string;
```

#### inCsv method.

Checks for a value within comma-separated values (CSV). Returns true when the value is found and false otherwise. This is used internally to process the `Disabled` property, and also made public for the benefit of use at the implementation elsewhere.

```PHP
public function inCsv(string $Value, string $CSV): bool;
```

#### sendMessage method.

When `SendToOut` is `true`, this method sends messages to `stdout` whenever a request is performed, in a manner similar to the entries seen within standard access logs (this can sometimes be useful for debugging).

```PHP
public function sendMessage(string $Message): void;
```

#### getCertPath method.

A private method used internally to fetch the path to the system's certificate file.

```PHP
private function getCertPath(): string;
```

---


Last Updated: 24 April 2026 (2026.04.24).
