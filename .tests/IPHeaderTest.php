<?php
/**
 * IP header class tests file.
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

/** Guard. */
if (!isset($_SERVER)) {
    $_SERVER = [];
}

/** Iterate through some test IP address headers. */
foreach ([
    ['Headers' => '2001:db8:85a3:8d3:1319:8a2e:370:7348', 'Should resolve as' => '2001:db8:85a3:8d3:1319:8a2e:370:7348', 'Type' => 6],
    ['Headers' => '203.0.113.195', 'Should resolve as' => '203.0.113.195', 'Type' => 4],
    ['Headers' => '203.0.113.195, 70.41.3.18, 150.172.238.178', 'Should resolve as' => '203.0.113.195', 'Type' => 4],
    ['Headers' => 'for="_mdn"', 'Should resolve as' => '', 'Type' => 0],
    ['Headers' => 'For="[2001:db8:cafe::17]:4711"', 'Should resolve as' => '2001:db8:cafe::17', 'Type' => 6],
    ['Headers' => 'for=192.0.2.60;proto=http;by=203.0.113.43', 'Should resolve as' => '192.0.2.60', 'Type' => 4],
    ['Headers' => 'for=192.0.2.43, for=198.51.100.17', 'Should resolve as' => '192.0.2.43', 'Type' => 4],
    ['Headers' => 'proto=http;by=203.0.113.43;for=192.0.2.61,for=198.51.100.17;', 'Should resolve as' => '192.0.2.61', 'Type' => 4],
    ['Headers' => 'garbage, 1.2.3.4.5, a:b, 1.1.1.999, 1.2.3.4, 9.8.7.6, garbage', 'Should resolve as' => '1.2.3.4', 'Type' => 4],
    ['Headers' => 'garbage,1.2.3.4.5,a:b,1.1.1.999,1.2.3.4,9.8.7.6,garbage','Should resolve as' => '1.2.3.4', 'Type' => 4]
] as $Fake) {
    $ExitCode++;
    $_SERVER['FakeIPAddressForTests'] = $Fake['Headers'];
    $Obj = new \Maikuolan\Common\IPHeader('FakeIPAddressForTests');
    if ($Fake['Should resolve as'] !== $Obj->Resolution) {
        echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
        var_dump($Fake['Should resolve as']);
        echo PHP_EOL . 'Actual: ';
        var_dump($Obj->Resolution);
        echo PHP_EOL;
        exit($ExitCode);
    }
    if ($Fake['Type'] !== $Obj->Type) {
        echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
        var_dump($Fake['Type']);
        echo PHP_EOL . 'Actual: ';
        var_dump($Obj->Type);
        echo PHP_EOL;
        exit($ExitCode);
    }
}

/** Cleanup. */
unset($Obj, $_SERVER['FakeIPAddressForTests']);
