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
    $Object->splitVersionParts('3.0.0-alpha3') !== [3, 0, 0, -4, 3] ||
    $Object->splitVersionParts('2021.0.1-DEV+123456') !== [2021, 0, 1, -5, 123456] ||
    $Object->splitVersionParts('4.5.6.7.8.9.10') !== [4, 5, 6, 7, 8, 9, 10] ||
    $Object->splitVersionParts('1.2.3') !== [1, 2, 3] ||
    $Object->splitVersionParts('v7.4') !== [7, 4, 0] ||
    $Object->splitVersionParts('v8.1-rc5') !== [8, 1, -2, 5]
) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Results = $Object->multiCompare(
    ['1.2.3', '1.2.3', '4.5.6', '4.5.6', '3.0.0-alpha3', '2021.0.1-DEV+123456', '', '7.7.7'],
    ['>=1.0.0 <2.0.0', '^1.2', '>=4.5.1 <4.5.9', '>=4.5.1&<4.5.9', '^2|^3.0.0-alpha2', '<=2022', '^0', '=7.7.7']
);

$ExitCode++;
if ($Results !== true) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Results = $Object->multiCompare(
    ['1.2.3', '4.5.6', '3.0.0-alpha3', '2021.0.1-DEV+123456'],
    ['<1', '>=4.6.1 <4.9', '^2', '>=2022']
);

$ExitCode++;
if ($Results !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
