<?php
/**
 * Delayed file IO tests file.
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

$Object = new \Maikuolan\Common\DelayedIO();

$Data = $Object->readFile($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'iotest.txt', LOCK_SH);
sleep(1);
$Data = hash('sha256', $Data);
$Expected = 'bf059f3112049d7299f9dc39397fe721c560e790611bfdc163adadbebb4e9ca9';

if ($Data !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Data = $Object->writeFile($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'iotest.txt', $Data, LOCK_EX);
unset($Object);
sleep(1);

$Confirm = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'iotest.txt');

$ExitCode++;
if ($Confirm !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
