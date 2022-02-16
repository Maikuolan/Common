<?php
/**
 * Tests loader.
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

// Tests directory.
$TestsDir = __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;

// Classes directory.
$ClassesDir = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

// Base directory.
$BaseDir = __DIR__ . DIRECTORY_SEPARATOR;

// Run tests.
foreach (['ComplexStringHandler', 'DelayedIO', 'Demojibakefier', 'Events', 'L10N', 'NumberFormatter', 'Operation', 'Request', 'YAML'] as $Case) {
    if (!is_readable($ClassesDir . $Case . '.php') || !is_readable($TestsDir . $Case . 'Test.php')) {
        echo $Case . '.php is not readable.' . PHP_EOL;
        exit(1);
    }
    $ExitCode = 2;
    require $TestsDir . $Case . 'Test.php';
}

echo 'All tests passed.' . PHP_EOL;
exit(0);
