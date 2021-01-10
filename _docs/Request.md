### Documentation for the "Request" class.

*Used by CIDRAM and phpMussel to send outbound requests through cURL.*

---


### How to use:

- [DefaultTimeout member.](#defaulttimeout-member)
- [Channels member.](#channels-member)
- [Disabled member.](#disabled-member)
- [SendToOut member.](#sendtoout-member)
- [UserAgent member.](#useragent-member)
- [MostRecentStatusCode member.](#mostrecentstatuscode-member)
- [request method.](#request-method)
- [inCsv method.](#incsv-method)
- [sendMessage method.](#sendmessage-method)

#### DefaultTimeout member.

Sets the default timeout to use for any requests which don't specify their own timeout.

```PHP
public $DefaultTimeout = 12;
```

#### Channels member.

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

You can also just ignore this member entirely if you don't want to utilise alternative channels at your implementation. 

#### Disabled member.

A CSV listing any alternative channels that should be disabled for the request (useful, for example, if you have a static list of alternative channels for your implementation, but provide the ability for end-users to optionally disable channels of their choice).

```PHP
public $Disabled = '';
```

*Example (per the earlier provided example):*
```
X example,Y example,Hello world,Lorem ipsum
```

#### SendToOut member.

Whether to send the results of outbound requests to stdout (useful for debugging, but most likely won't ever be needed in production).

```PHP
public $SendToOut = false;
```

#### UserAgent member.

The default user agent to cite when sending requests (for the sake of good netiquette and politeness towards any endpoints you intend to communicate with, this should definitely be populated when implementing the class according to your implementation).

```PHP
public $UserAgent = 'Request class (https://github.com/Maikuolan/Common)';
```

#### MostRecentStatusCode member.

Whenever a request is performed, the status code returned by that request will be populated to this member (e.g., 200, 403, 404, etc).

```PHP
public $MostRecentStatusCode = 0;
```

#### request method.

The main request method (this is what you'll want to use to actually perform a request).

```PHP
public function request(string $URI, $Params = [], int $Timeout = -1, array $Headers = [], int $Depth = 0): string
```

The first parameter (`$URI`) is the URL, URI, resource, etc that you want to request.

The second parameter (`$Params`) is for any parameters you want to send along with your request. If empty or omitted, `CURLOPT_POST` is `false`. Otherwise, `CURLOPT_POST` is true, and the parameter is used to supply `CURLOPT_POSTFIELDS`. Normally an associative array of key-value pairs, but can be any kind of value supported by `CURLOPT_POSTFIELDS`. Optional.

The third parameter (`$Timeout`) is an optional timeout limit for the request. When omitted, `DefaultTimeout` is used instead.

The fourth parameter (`$Headers`) is an optional array of headers to send with the request.

The fifth parameter (`$Depth`) represents the recursion depth of the current request instance, is populated automatically by `request`, and shouldn't ever be populated manually by the implementation.

The method returns a string (either the returned resource, or an empty string on failure).

The class also implements the magic method `__invoke`, as a way to alias back to `request` when the instance is utilised as a callable or function.

```PHP
public function __invoke(...$Params): string
```

#### inCsv method.

Checks for a value within comma-separated values (CSV). Returns true when the value is found and false otherwise. This is used internally to process the `Disabled` member, and also made public for the benefit of use at the implementation elsewhere.

```PHP
public function inCsv(string $Value, string $CSV): bool
```

#### sendMessage method.

When `SendToOut` is `true`, this method sends messages to `stdout` whenever a request is performed, in a manner similar to the entries seen within standard access logs (this can sometimes be useful for debugging).

```PHP
public function sendMessage(string $Message);
```

---


Last Updated: 10 January 2021 (2021.01.10).
