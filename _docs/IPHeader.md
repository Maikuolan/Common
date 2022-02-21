### Documentation for the "IPHeader" class.

*Attempts to resolve an originating IP address from a preferred source, or `REMOTE_ADDR` if the preferred source isn't available.*

---


### How to use:

Instantiate the class, supplying as a parameter to the constructor the name of the `$_SERVER` variable (i.e., the name of the header) that you want the class to prefer as the source for the request's originating IP address.

Example:

```PHP
<?php
$Obj = new \Maikuolan\Common\IPHeader('X-Forwarded-For');
```

Some suggested sources to use: | Normally used for:
---|---
`HTTP_INCAP_CLIENT_IP` | Incapsula reverse proxy.
`HTTP_CF_CONNECTING_IP` | Cloudflare reverse proxy.
`CF-Connecting-IP` | Cloudflare reverse proxy (alternative; if the above doesn't work).
`HTTP_X_FORWARDED_FOR` | Cloudbric reverse proxy.
`X-Forwarded-For` | [Squid reverse proxy](http://www.squid-cache.org/Doc/config/forwarded_for/).
`Forwarded` | *[Forwarded - HTTP \| MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Forwarded).*
`REMOTE_ADDR` | Used in about ~99% of cases normally (and the class also falls back to this source anyway if the preferred source specified isn't available).

After instantiating the class, the object will provide three public members (or properties).

```PHP
public $Resolution = '';
```

The `Resolution` property is a string describing the IP address resolved by the instance. It'll contain a valid IPv4 or IPv6 address, or if the instance failed to resolve an IP address, an empty string.

You can use the `Resolution` property to access the resolved IP address.

Example:

```PHP
<?php
$_SERVER['Foobar'] = 'for=192.0.2.60;proto=http;by=203.0.113.43';
$Obj = new \Maikuolan\Common\IPHeader('Foobar');
echo $Obj->Resolution;
// Uses "Foobar" to produce: 192.0.2.60

$_SERVER['Foobar'] = 'Not a valid IP address';
$_SERVER['REMOTE_ADDR'] = 'proto=http;by=203.0.113.43;for=192.0.2.61,for=198.51.100.17;';
$Obj = new \Maikuolan\Common\IPHeader('Foobar');
echo $Obj->Resolution;
// Falls back to "REMOTE_ADDR" to produce: 192.0.2.61
```

```PHP
public $Source = '';
```

The `Source` property is a string describing where the resolved IP address was ultimately sourced from (generally, either the preferred source specified, or `REMOTE_ADDR`). If the instance failed to resolve an IP address, it'll be an empty string.

```PHP
public $Type = 0;
```

The `Type` property is an integer describing the type of IP address resolved: `4` for IPv4, `6` for IPv6, or `0` otherwise (e.g., if the instance failed to resolve an IP address).

---


Last Updated: 21 February 2022 (2022.02.21).
