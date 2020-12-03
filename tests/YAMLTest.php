<?php
/**
 * YAML handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$Expected = [
    'String foo' => 'Bar',
    'Integer foo' => 1234,
    'Float foo' => 123.4,
    'Example numeric array' => [
        0 => 'Bar1',
        1 => 'Bar2',
        2 => 'Bar3',
        3 => 'Bar4',
    ],
    'Example associative array' => [
        'Foo1' => 'Bar1',
        'Foo2' => 'Bar2',
        'Foo3' => 'Bar3',
        'Foo4' => 'Bar4',
    ],
    'Example mixed multi-dimensional array' => [
        0 => 'Bar1',
        1 => 'Bar2',
        'xFooX' => 'xBarX',
        'Some int' => 4567,
        'Sub array' => [
            'Hello' => 'World',
            'Sub-sub array' => [
                'Foobar' => 'Barfoo',
            ],
        ],
    ],
    'Example hex-encoded data' => 'Hello World (but in hex)',
    'Multi-line example' => "h e l l o - w o r l d\nhello-world",
];

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'example.yaml');

$Object = new \Maikuolan\Common\YAML($RawYAML);

if ($Expected !== $Object->Data) {
    echo 'Test failed: ' . $Case . '().' . PHP_EOL;
    exit(9);
}

$Object = new \Maikuolan\Common\YAML();

$ProcessResult = $Object->process($RawYAML, $Object->Data);

if ($ProcessResult !== true) {
    echo 'Test failed: ' . $Case . '().' . PHP_EOL;
    exit(10);
}

if ($Expected !== $Object->Data) {
    echo 'Test failed: ' . $Case . '().' . PHP_EOL;
    exit(11);
}

$InvalidYAML = 1000;

$NoNewLineYAML = "No new end of line";

$Object = new \Maikuolan\Common\YAML();

if ($Object->process($InvalidYAML, $Object->Data) !== false) {
    echo 'Test failed: ' . $Case . '().' . PHP_EOL;
    exit(12);
}

if ($Object->process($NoNewLineYAML, $Object->Data) !== false) {
    echo 'Test failed: ' . $Case . '().' . PHP_EOL;
    exit(13);
}
