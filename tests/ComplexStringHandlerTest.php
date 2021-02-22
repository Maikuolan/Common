<?php
/**
 * Complex string handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$TheString = 'ab0cd1ef2gh3ij4kl5mn6op7qr8st9uv';
$ThePattern = '~(\D+)~';

$ComplexStringHandler = new \Maikuolan\Common\ComplexStringHandler($TheString, $ThePattern, function ($Data) {
    return ($Data === '' || $Data === false) ? '' : ' "' . (((int)$Data + 1)) . '" ';
});
$ComplexStringHandler->iterateClosure(function ($Data) {
    return '(' . $Data . ')';
}, true);

if ('(ab) "1" (cd) "2" (ef) "3" (gh) "4" (ij) "5" (kl) "6" (mn) "7" (op) "8" (qr) "9" (st) "10" (uv)' !== $ComplexStringHandler->recompile()) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;

$ComplexStringHandler = new \Maikuolan\Common\ComplexStringHandler();
$ComplexStringHandler->Input = $TheString;
$ComplexStringHandler->generateMarkers($ThePattern);
$ComplexStringHandler->iterateClosure(function ($Data) {
    return ($Data === '' || $Data === false) ? '' : ' "' . (((int)$Data + 1)) . '" ';
}, false);
$ComplexStringHandler->iterateClosure(function ($Data) {
    return '(' . $Data . ')';
}, true);

if ('(ab) "1" (cd) "2" (ef) "3" (gh) "4" (ij) "5" (kl) "6" (mn) "7" (op) "8" (qr) "9" (st) "10" (uv)' !== $ComplexStringHandler->recompile()) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
