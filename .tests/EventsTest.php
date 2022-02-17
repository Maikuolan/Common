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

$Object = new \Maikuolan\Common\Events();

$TestData = ['TestString' => ''];

$Object->addHandler('testEvent', function ($Data) use (&$TestData) {
    $TestData['Foo'] = 'Bar';
    $TestData['TestString'] .= $Data;
});

$Object->addHandlerFinal('testEventFinal', function ($Data) use (&$TestData) {
    $TestData['FooFinal'] = 'Bar';
    $TestData['TestString'] .= $Data;
});

$Object->addHandler('testEvent', function () use (&$TestData) {
    $TestData['Baz'] = 'Far';
});

$Object->addHandlerFinal('testEventFinal', function () use (&$TestData) {
    $TestData['BazFinal'] = 'Far';
});

if (
    $Object->assigned('testEvent') !== true ||
    $Object->assigned('testEventFinal') !== true ||
    $Object->assigned('fakeEvent') !== false
) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object->destroyEvent('testEvent');

$ExitCode++;
if ($Object->assigned('testEvent') !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if (isset($TestData['Foo']) || isset($TestData['FooFinal'])) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object->fireEvent('testEventFinal', 'This is a test. :-)');

$ExitCode++;
if (isset($TestData['BazFinal'])) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if (!isset($TestData['FooFinal'])) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if (!isset($TestData['TestString']) || $TestData['TestString'] !== 'This is a test. :-)') {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
