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
