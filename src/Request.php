<?php
/**
 * Request handler (last modified: 2022.02.12).
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * @link https://github.com/Maikuolan/Common
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 *
 * "COMMON CLASSES PACKAGE" COPYRIGHT 2019 and beyond by Caleb Mazalevskis.
 * *This particular class*, COPYRIGHT 2021 and beyond by Caleb Mazalevskis.
 */

namespace Maikuolan\Common;

class Request
{
    /**
     * @var int The default timeout to use when one isn't specified for a given request.
     */
    public $DefaultTimeout = 12;

    /**
     * @var array Alternative channels to use for requests matching specific patterns.
     */
    public $Channels = ['Triggers' => []];

    /**
     * @var string Disabled channels CSV.
     */
    public $Disabled = '';

    /**
     * @var bool Whether to send the results of outbound requests to stdout.
     */
    public $SendToOut = false;

    /**
     * @var string The default user agent to cite.
     */
    public $UserAgent = 'Request class (https://github.com/Maikuolan/Common)';

    /**
     * @var int The most recent status code generated by the instance.
     */
    public $MostRecentStatusCode = 0;

    /**
     * @var string The tag/release the version of this file belongs to (might
     *      be needed by some implementations to ensure compatibility).
     * @link https://github.com/Maikuolan/Common/tags
     */
    const VERSION = '1.7.0';

    /**
     * Allow calling the instance as a function (proxies to request).
     *
     * @return string
     */
    public function __invoke($URI, $Params = [], $Timeout = -1, array $Headers = [], $Depth = 0)
    {
        return $this->request($URI, $Params, $Timeout, $Headers, $Depth);
    }

    /**
     * The main request method.
     *
     * @param string $URI The resource to request.
     * @param mixed $Params If empty or omitted, CURLOPT_POST is false. Otherwise,
     *      CURLOPT_POST is true, and the parameter is used to supply
     *      CURLOPT_POSTFIELDS. Normally an associative array of key-value pairs,
     *      but can be any kind of value supported by CURLOPT_POSTFIELDS. Optional.
     * @param int $Timeout An optional timeout limit.
     * @param array $Headers An optional array of headers to send with the request.
     * @param int $Depth Recursion depth of the current closure instance.
     * @return string The results of the request, or an empty string upon failure.
     */
    public function request($URI, $Params = [], $Timeout = -1, array $Headers = [], $Depth = 0)
    {
        /** Test channel triggers. */
        foreach ($this->Channels['Triggers'] as $TriggerName => $TriggerURI) {
            if (
                !isset($this->Channels[$TriggerName]) ||
                !is_array($this->Channels[$TriggerName]) ||
                substr($URI, 0, strlen($TriggerURI)) !== $TriggerURI
            ) {
                continue;
            }
            foreach ($this->Channels[$TriggerName] as $Channel => $Options) {
                if (!is_array($Options) || !isset($Options[$TriggerName])) {
                    continue;
                }
                $Len = strlen($Options[$TriggerName]);
                if (substr($URI, 0, $Len) !== $Options[$TriggerName]) {
                    continue;
                }
                unset($Options[$TriggerName]);
                if (empty($Options) || $this->inCsv(key($Options), $this->Disabled)) {
                    continue;
                }
                $AlternateURI = current($Options) . substr($URI, $Len);
                break;
            }
            if ($this->inCsv($TriggerName, $this->Disabled)) {
                if (isset($AlternateURI)) {
                    return $this($AlternateURI, $Params, $Timeout, $Headers, $Depth);
                }
                return '';
            }
            if (isset($this->Channels['Overrides'], $this->Channels['Overrides'][$TriggerName])) {
                $Overrides = $this->Channels['cURL Overrides'][$TriggerName];
            }
            break;
        }

        /** Empty overrides in case none declared. */
        $Overrides = [];

        /** Initialise the cURL session. */
        $Request = curl_init($URI);

        $LCURI = strtolower($URI);
        $SSL = (substr($LCURI, 0, 6) === 'https:');

        curl_setopt($Request, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($Request, CURLOPT_HEADER, false);
        if (empty($Params)) {
            curl_setopt($Request, CURLOPT_POST, false);
            $Post = false;
        } else {
            curl_setopt($Request, CURLOPT_POST, true);
            curl_setopt($Request, CURLOPT_POSTFIELDS, $Params);
            $Post = true;
        }
        if ($SSL) {
            curl_setopt($Request, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
            curl_setopt($Request, CURLOPT_SSL_VERIFYPEER, (
                isset($Overrides['CURLOPT_SSL_VERIFYPEER']) ? !empty($Overrides['CURLOPT_SSL_VERIFYPEER']) : false
            ));
        }
        curl_setopt($Request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($Request, CURLOPT_MAXREDIRS, 1);
        curl_setopt($Request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($Request, CURLOPT_TIMEOUT, ($Timeout > 0 ? $Timeout : $this->DefaultTimeout));
        curl_setopt($Request, CURLOPT_USERAGENT, $this->UserAgent);
        curl_setopt($Request, CURLOPT_HTTPHEADER, $Headers ?: []);
        $Time = microtime(true);

        /** Execute and get the response. */
        $Response = curl_exec($Request);

        $Time = microtime(true) - $Time;

        /** Check for problems (e.g., resource not found, server errors, etc). */
        if (($Info = curl_getinfo($Request)) && is_array($Info) && isset($Info['http_code'])) {
            $this->sendMessage(sprintf(
                "\r%s - %s - %s - %s\n",
                $Post ? 'POST' : 'GET',
                $URI,
                $Info['http_code'],
                (floor($Time * 100) / 100) . 's'
            ));

            /** Most recent HTTP status code. */
            $this->MostRecentStatusCode = $Info['http_code'];

            /** Request failed. Try again using an alternative address. */
            if ($Info['http_code'] >= 400 && isset($AlternateURI) && $Depth < 3) {
                curl_close($Request);
                return $this($AlternateURI, $Params, $Timeout, $Headers, $Depth + 1);
            }
        } else {
            $this->sendMessage(sprintf(
                "\r%s - %s - %s - %s\n",
                $Post ? 'POST' : 'GET',
                $URI,
                200,
                (floor($Time * 100) / 100) . 's'
            ));

            /** Most recent HTTP status code. */
            $this->MostRecentStatusCode = 200;
        }

        /** Close the cURL session. */
        curl_close($Request);

        /** Return the results of the request. */
        return $Response;
    }

    /**
     * Checks for a value within CSV.
     *
     * @param string $Value The value to look for.
     * @param string $CSV The CSV to look in.
     * @return bool True when found; False when not found.
     */
    public function inCsv($Value, $CSV)
    {
        if (!$Value || !$CSV) {
            return false;
        }
        $Arr = explode(',', $CSV);
        if (strpos($CSV, '"') !== false) {
            foreach ($Arr as &$Item) {
                if (substr($Item, 0, 1) === '"' && substr($Item, -1) === '"') {
                    $Item = substr($Item, 1, -1);
                }
            }
        }
        return in_array($Value, $Arr, true);
    }

    /**
     * Sends messages to stdout.
     *
     * @param string $Message The message to send.
     * @return void
     */
    public function sendMessage($Message)
    {
        if ($this->SendToOut !== true) {
            return;
        }
        $Handle = fopen('php://stdout', 'wb');
        fwrite($Handle, $Message);
        fclose($Handle);
    }
}
