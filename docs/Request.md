### Documentation for the request handler.

*Used by CIDRAM and phpMussel to send outbound requests through cURL.*

---


### Some simple examples.

```PHP
// Creates a new request handler instance.
$Request = new \Maikuolan\Common\Request();

// Sets the instance's default request timeout to 30 seconds (the class's default timeout is 12 seconds).
$Request->DefaultTimeout = 30;

// Sets a custom user agent.
$Request->UserAgent = 'A custom user agent for my app or project';

// Sends an HTTP POST request to a website with some arbitrary POST fields, using the timeout, user agent, etc that we set before. Response is saved to $Var.
$Var = $Request->request('https://example.com/', ['foo' => 'bar', 'foz' => 'baz']);

// Sends the same HTTP POST request but with a custom timeout of 5 seconds. Response is saved to $Var.
$Var = $Request->request('https://example.com/', ['foo' => 'bar', 'foz' => 'baz'], 5);

// Sends the same HTTP POST request but with a custom timeout of 5 seconds and a custom X-PoweredBy header. Response is saved to $Var.
$Var = $Request->request('https://example.com/', ['foo' => 'bar', 'foz' => 'baz'], 5, ['X-PoweredBy' => 'My cool app']);

// Sends an HTTP GET request to a website. Response is saved to $Var.
$Var = $Request->request('https://example.com/');

// Sends the same HTTP GET request but with a custom timeout of 10 seconds. Response is saved to $Var.
$Var = $Request->request('https://example.com/', [], 3);

// Sends the same HTTP GET request but with a custom X-PoweredBy header and no custom timeout (just using the instance's default timeout we set earlier). Response is saved to $Var.
$Var = $Request->request('https://example.com/', [], -1, ['X-PoweredBy' => 'My cool app']);

// What was the HTTP status code of that very last request? Let's find out.
$Code = $Request->MostRecentStatusCode;

// What if I need to request something from an FTP server? We can do that, too.
$Var = $Request->request('ftp://example.com/some-file.txt');

// What if that FTP server needs a username and password? We can supply that if needed.
$Var = $Request->request('ftp://example.com/some-file.txt', ['USERPWD' => 'some-username:some-password']);
```

The request handler can handle plenty of other kinds of requests and situations where requests are needed, too, but those examples should be enough to convey the general idea.

---


### Class properties, constants, and methods:

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
- [AllowCurl property.](#allowcurl-property)
- [AllowFOpenWStream property.](#allowfopenwstream-property)
- [AllowFSockOpenWStream property.](#allowfsockopenwstream-property)
- [DF property.](#df-property)
- [Supported property.](#supported-property)
- [Time property.](#time-property)
- [STREAM_BLOCKSIZE constant.](#stream_blocksize-constantproperty)
- [request method.](#request-method)
- [inCsv method.](#incsv-method)
- [sendMessage method.](#sendmessage-method)
- [getCertPath method.](#getcertpath-method)
- [timer method.](#timer-method)

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

#### AllowCurl property.

Whether to allow using curl for sending requests. Automatically set during instantiation based on the functionality available, but can be manually set by the implementation if so desired.

```PHP
public $AllowCurl = false;
```

#### AllowFOpenWStream property.

Whether to allow using fopen with streams for sending requests. Automatically set during instantiation based on the functionality available, but can be manually set by the implementation if so desired.

```PHP
public $AllowFOpenWStream = false;
```

#### AllowFSockOpenWStream property.

Whether to allow using fsockopen with streams for sending requests. Automatically set during instantiation based on the functionality available, but can be manually set by the implementation if so desired.

```PHP
public $AllowFSockOpenWStream = false;
```

#### DF property.

A private property populated during instantiation, used internally to check whether specific PHP functions are disabled.

```PHP
private $DF = [];
```

#### Supported property.

A private property populated during instantiation, used internally to check the protocol or socket transport specified for any given request against which protocols and socket transports are supported by the class.

```PHP
private $Supported = [1 => [], 2 => ['FTP' => 1, 'FTPS' => 1, 'HTTP' => 1, 'HTTPS' => 1], 3 => ['TCP' => 1, 'UDP' => 1]];
```

Supported when using curl (others *potentially* may also work if your curl installation is configured accordingly, but as the class hasn't been coded specifically with others in mind, such isn't guaranteed):
- FTP
- FTPS
- GOPHER
- HTTP
- HTTPS
- SFTP
- TFTP

Supported when using `fopen` with streams:
- FTP
- FTPS
- HTTP
- HTTPS

Supported when using `fsockopen` with streams:
- TCP
- UDP

Additionally supported:
- FILE (processed using `file_get_contents`)

#### Time property.

Used by the `timer` method.

```PHP
private $Time = 0.0;
```

#### STREAM_BLOCKSIZE constant.

A private constant representing how many bytes to read at a time when reading the response to a request sent using `fopen` with `stream_context_create`.

```PHP
private const STREAM_BLOCKSIZE = 131072;
```

#### request method.

The main request method (this is what you'll want to use to actually perform a request).

```PHP
public function request(string $URI, array $Params = [], int $Timeout = -1, array $Headers = [], int $Depth = 0, string $Method = '', int $MaxSegments = -1): string;
```

The first parameter (`$URI`) is the URL or URI of the resource that you want to request.

When sending an HTTP/S request, the second parameter (`$Params`) is an optional, associative array of key-value pairs for any POST fields you may want to send along with your request.

- When using curl: If provided and not empty, `CURLOPT_POST` will be `true`, and the POST fields will be populated to `CURLOPT_POSTFIELDS`. Otherwise, `CURLOPT_POST` will be `false`. 
- When using `fopen` with streams: If provided and not empty, the POST fields will be populated to the stream context.

When sending an FTP/S request, if sending the request to a server requiring a username and password, include an element with the key `USERPWD`, the value containing the required username and password as `Username:Password`.

- When using curl: `USERPWD` (if provided) will be populated to `CURLOPT_USERPWD`.
- When using `fopen` with streams: `USERPWD` (if provided) will be infixed to the value from `$URI` when supplied to the resource handle (e.g., if `USERPWD` is `admin:password`, a `$URI` of `ftps://example.tld:21/foobar.txt` would see `ftps://admin:password@example.tld:21/foobar.txt` be supplied to the resource handle). This won't have any effect on data written to `ObjLogger` or `stdout` by the `sendMessage` method, as the `request` method supplies to it the provided `$URI` value verbatim, sans infix. However, the infix *could* potentially appear in logs at the server hosting the resource, and so could potentially be less secure than when using curl in some cases.

When sending a TCP or UDP request (i.e., when using `fsockopen` with streams), the port number can be specified by including an element with the key `Port` and the value containing the port number, and the message to send can be specified by including an element with the key `Message` and the value containing the message to send.

The third parameter (`$Timeout`) is an optional timeout limit for the request. When omitted, `DefaultTimeout` is used instead.

The fourth parameter (`$Headers`) is an optional array of headers to send with the request. Only relevant when sending HTTP/S requests and should generally be omitted otherwise.

The fifth parameter (`$Depth`) represents the recursion depth of the current request instance, is populated automatically by `request`, and shouldn't be populated manually by the implementation (other than when needing access to the sixth parameter).

The sixth parameter (`$Method`) can be used to specify the intended request method in the event that the method intended isn't GET or POST. When the intended request method is GET or POST, it shouldn't be populated manually by the implementation (the method will determine automatically whether GET or POST is needed, based on factors like request parameters). This can be useful when methods such as CONNECT or DELETE are needed.

The seventh parameter (`$MaxSegments`) can be used to specify the maximum number of segments to receive (this can sometimes be useful to prevent timeouts for things like DNS lookups, but only very rarely is actually needed).

The method returns a string (the response to the request upon success, or an empty string upon failure).

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

#### timer method.

Starts or stops the timer for message logging.

```PHP
private function timer(bool $OnOff = true): string;
```

---


Last Updated: 1 May 2026 (2026.05.01).
