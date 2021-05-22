<?php
/**
 * Matrix handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$Object = new \Maikuolan\Common\Matrix();
$Object->createMatrix(3, 3, 'Foobar');

if ($Object->Matrix !== [
    [['Foobar', 'Foobar', 'Foobar'], ['Foobar', 'Foobar', 'Foobar'], ['Foobar', 'Foobar', 'Foobar']],
    [['Foobar', 'Foobar', 'Foobar'], ['Foobar', 'Foobar', 'Foobar'], ['Foobar', 'Foobar', 'Foobar']],
    [['Foobar', 'Foobar', 'Foobar'], ['Foobar', 'Foobar', 'Foobar'], ['Foobar', 'Foobar', 'Foobar']]
]) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object->iterateCallback('0-2,0-2,0-2', function (&$Current, $Key) {
    $Current = $Key;
});

$ExitCode++;
if ($Object->Matrix !== [
    [['0,0,0', '0,0,1', '0,0,2'], ['0,1,0', '0,1,1', '0,1,2'], ['0,2,0', '0,2,1', '0,2,2']],
    [['1,0,0', '1,0,1', '1,0,2'], ['1,1,0', '1,1,1', '1,1,2'], ['1,2,0', '1,2,1', '1,2,2']],
    [['2,0,0', '2,0,1', '2,0,2'], ['2,1,0', '2,1,1', '2,1,2'], ['2,2,0', '2,2,1', '2,2,2']]
]) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
