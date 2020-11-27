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
foreach (['ComplexStringHandler', 'L10N', 'YAML'] as $Case) {
    if (!is_readable($ClassesDir . $Case . '.php') || !is_readable($TestsDir . $Case . 'Test.php')) {
        exit(1);
    }
    require $TestsDir . $Case . 'Test.php';
}

// All tests passed.
exit(0);
