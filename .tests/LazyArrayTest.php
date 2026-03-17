<?php
/**
 * Lazy process handler for arrays tests file.
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

$Object = new \Maikuolan\Common\LazyArray(function ($Data) {
    return explode(',', $Data);
}, 'foo,bar,baz');
$Object->trigger();
if ($Object->Data !== ['foo', 'bar', 'baz']) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump(['foo', 'bar', 'baz']);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
    exit($ExitCode);
}

$Object = new \Maikuolan\Common\LazyArray(function ($Data) {
    return explode(',', $Data);
}, 'foo,bar,baz');
$ExitCode++;
if ($Object[0] !== 'foo' || $Object[1] !== 'bar' || $Object[2] !== 'baz') {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object['test'] = 'testing';
$ExitCode++;
if ($Object['test'] !== 'testing') {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

/** Test will fail for PHP versions < 7.4, as it relies on magic methods introduced since PHP 7.4. */
if (\PHP_VERSION_ID >= 70400) {
    $Object = serialize($Object);
    $Expected = 'O:26:"Maikuolan\Common\LazyArray":4:{i:0;s:3:"foo";i:1;s:3:"bar";i:2;s:3:"baz";s:4:"test";s:7:"testing";}';
    $ExitCode++;
    if ($Object !== $Expected) {
        echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ' . $Expected;
        echo PHP_EOL . 'Actual: ' . $Object . PHP_EOL;
        exit($ExitCode);
    }
}
