<?php
/**
 * Tests loader.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

// Prevent running tests outside of Composer (if the package is deployed
// somewhere live with this file still intact, useful to prevent hammering and
// cycles being needlessly wasted).
if (!isset($_SERVER['COMPOSER_BINARY'])) {
    die;
}

// Tests directory.
$TestsDir = __DIR__ . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;

// Classes directory.
$ClassesDir = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

// Run tests.
foreach (['ComplexStringHandler', 'DelayedIO', 'Demojibakefier', 'Events', 'L10N', 'Matrix', 'NumberFormatter', 'Operation', 'Request', 'YAML'] as $Case) {
    if (!is_readable($ClassesDir . $Case . '.php') || !is_readable($TestsDir . $Case . 'Test.php')) {
        echo $Case . '.php is not readable.' . PHP_EOL;
        exit(1);
    }
    $ExitCode = 2;
    require $TestsDir . $Case . 'Test.php';
}

echo 'All tests passed.' . PHP_EOL;
exit(0);
