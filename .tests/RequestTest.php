<?php
/**
 * Events orchestrator IO tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * @link https://github.com/Maikuolan/Common
 */

/**
 * If this file remains intact after deploying the package to production,
 * preventing it from running outside of Composer may be useful as a means of
 * prevent potential attackers from hammering the file and needlessly wasting
 * cycles at the server.
 */
if (!isset($_SERVER['COMPOSER_BINARY'])) {
    die;
}

require $ClassesDir . $Case . '.php';

$Object = new \Maikuolan\Common\Request();

/**
 * TLS certificate verification: a local HTTPS server with a self-signed
 * certificate must be refused by default (by both curl and the fopen fallback).
 * Runs offline; skipped when openssl or proc_open aren't available.
 */
if (
    \extension_loaded('openssl') &&
    \function_exists('proc_open') &&
    ($TlsTestDir = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'common-tls-test-' . \getmypid()) &&
    @\mkdir($TlsTestDir)
) {
    $TlsCnf = $TlsTestDir . DIRECTORY_SEPARATOR . 'openssl.cnf';
    $TlsPem = $TlsTestDir . DIRECTORY_SEPARATOR . 'server.pem';
    \file_put_contents($TlsCnf, "[req]\ndistinguished_name=dn\n[dn]\n[san]\nsubjectAltName=DNS:localhost\n");
    $TlsOptions = ['config' => $TlsCnf, 'digest_alg' => 'sha256', 'x509_extensions' => 'san'];
    $TlsKey = \openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA, 'config' => $TlsCnf]);
    $TlsCsr = $TlsKey ? \openssl_csr_new(['commonName' => 'localhost'], $TlsKey, $TlsOptions) : false;
    $TlsCert = $TlsCsr ? \openssl_csr_sign($TlsCsr, null, $TlsKey, 1, $TlsOptions) : false;
    if ($TlsCert && \openssl_x509_export($TlsCert, $TlsCertPem) && \openssl_pkey_export($TlsKey, $TlsKeyPem, null, ['config' => $TlsCnf])) {
        \file_put_contents($TlsPem, $TlsCertPem . $TlsKeyPem);

        /** A tiny HTTPS server on a free loopback port; prints the port, then answers every request. */
        $TlsServerCode = '$s = stream_socket_server("tls://127.0.0.1:0", $e, $m, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, ' .
            'stream_context_create(["ssl" => ["local_cert" => ' . \var_export($TlsPem, true) . ']])); ' .
            'if (!$s) { exit(1); } echo substr(strrchr(stream_socket_get_name($s, false), ":"), 1), "\n"; flush(); ' .
            'for ($i = 0; $i < 4; $i++) { $c = @stream_socket_accept($s, 10); if ($c === false) { continue; } fgets($c); ' .
            'fwrite($c, "HTTP/1.1 200 OK\r\nContent-Length: 10\r\nConnection: close\r\n\r\nUntrusted."); fclose($c); }';
        $TlsServer = \proc_open(
            (DIRECTORY_SEPARATOR === '/' ? 'exec ' : '') . \escapeshellarg(PHP_BINARY) . ' -r ' . \escapeshellarg($TlsServerCode),
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $TlsPipes
        );
        $TlsPort = \is_resource($TlsServer) ? (int)\trim((string)\fgets($TlsPipes[1])) : 0;

        if ($TlsPort > 0) {
            $TlsObject = new \Maikuolan\Common\Request();
            $TlsResults = [];
            if ($TlsObject->AllowCurl) {
                $TlsResults['curl'] = $TlsObject->request('https://localhost:' . $TlsPort . '/', [], 5);
            }
            if ($TlsObject->AllowFOpenWStream) {
                $TlsObject->AllowCurl = false;
                $TlsResults['fopen'] = @$TlsObject->request('https://localhost:' . $TlsPort . '/', [], 5);
            }
        }

        if (\is_resource($TlsServer)) {
            \proc_terminate($TlsServer);
            \proc_close($TlsServer);
        }
    }
    @\unlink($TlsPem);
    @\unlink($TlsCnf);
    @\rmdir($TlsTestDir);

    if (isset($TlsResults)) {
        foreach ($TlsResults as $TlsResult) {
            $ExitCode++;
            if ($TlsResult !== '') {
                echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
                exit($ExitCode);
            }
        }
    }
}

$ExitCode++;

$HelloWorld = $Object->request('https://raw.githubusercontent.com/Maikuolan/Common/v2/.tests/fixtures/iotest.txt');

if ($HelloWorld !== "Hello World.\n") {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if ($Object->MostRecentStatusCode !== 200) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$HelloWorld = $Object->request('https://raw.githubusercontent.com/Maikuolan/Common/v2/.tests/fixtures/404test.txt');

$ExitCode++;
if ($Object->MostRecentStatusCode !== 404) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$CSVData = 'qwe,rty,asd,dfg,zxc,cvb';

$ExitCode++;
if ($Object->inCsv('zzz', $CSVData) !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if ($Object->inCsv('qwe', $CSVData) !== true || $Object->inCsv('dfg', $CSVData) !== true || $Object->inCsv('cvb', $CSVData) !== true) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
