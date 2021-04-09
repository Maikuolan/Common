<?php
/**
 * Operation handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$Object = new \Maikuolan\Common\Operation();

if (
    $Object->splitVersionParts('3.0.0-alpha3') !== [3, 0, 0, -3, 3] ||
    $Object->splitVersionParts('2021.0.1-DEV+123456') !== [2021, 0, 1, -1, 123456] ||
    $Object->splitVersionParts('4.5.6.7.8.9.10') !== [4, 5, 6, 7, 8, 9, 10] ||
    $Object->splitVersionParts('1.2.3') !== [1, 2, 3] ||
    $Object->splitVersionParts('v7.4') !== [7, 4, 0] ||
    $Object->splitVersionParts('v8.1-rc5') !== [8, 1, 0, -1, 5]
) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object->Sources = [
    'Foo' => '1.2.3',
    'Bar' => '4.5.6',
    'Hello' => '3.0.0-alpha3',
    'World' => '2021.0.1-DEV+123456'
];

$Results = $Object->arraySourcesCompare([
    'Foo' => '>=1.0.0 <2.0.0',
    'Bar' => '>=4.5.1 <4.5.9',
    'Hello' => '^2|^3.0.0-alpha2',
    'World' => '<=2022'
]);

$ExitCode++;
if ($Results !== true) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Results = $Object->arraySourcesCompare([
    'Foo' => '^1.2',
    'Bar' => '>=4.5.1&<4.5.9',
    'Hello' => '^2 | ^3.0.0-alpha2'
]);

$ExitCode++;
if ($Results !== true) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Results = $Object->arraySourcesCompare([
    'Foo' => '<1',
    'Bar' => '>=4.6.1 <4.9',
    'Hello' => '^2',
    'World' => '>=2022'
]);

$ExitCode++;
if ($Results !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Results = $Object->arraySourcesCompare([
    'Foo' => '>=1.0.0 <2.0.0',
    'Bar' => '>=4.5.1 <4.5.9',
    'Hello' => '^3',
    'World' => '11.22.33'
]);

$ExitCode++;
if ($Results !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
