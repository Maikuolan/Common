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
    'Deep outermost' => [
        'Deep outer' => [
            'Deep inner' => [
                'Deep innermost' => 'Thus, from here, within this variable, a value is inserted'
            ]
        ]
    ],
    'String foo' => 'Bar',
    'Integer foo' => 1234,
    'Float foo' => 123.4,
    'Example numeric array' => [
        0 => 'Bar1',
        1 => 'Bar2',
        2 => 'Bar3',
        3 => 'Bar4'
    ],
    'Example associative array' => [
        'Foo1' => 'Bar1',
        'Foo2' => 'Bar2',
        'Foo3' => 'Bar3',
        'Foo4' => 'Bar4'
    ],
    'Example mixed multi-dimensional array' => [
        0 => 'Bar1',
        1 => 'Bar2',
        'xFooX' => 'xBarX',
        'Some int' => 4567,
        'Sub array' => [
            'Hello' => 'World',
            'Sub-sub array' => [
                'Foobar' => 'Barfoo'
            ]
        ]
    ],
    'Example hex-encoded data' => "Hello World (but in hex)\0",
    'Multi-line example' => "h e l l o - w o r l d\nhello-world",
    'Folded multi-line example' => 'Hello world. This is an example.',
    'Example booleans and null' => [
        'This is true' => true,
        'This is also true' => true,
        'This is false' => false,
        'This is also false' => false,
        'This is null' => null,
        'This is also null' => null
    ],
    'Anchored text push' => 'Some placeholder text.',
    'Anchored text pull' => 'Some placeholder text.',
    'Inserts test' => 'Hello world; Thus, from here, within this variable, a value is inserted; It should work, hopefully.',
    'Inline array example' => ['this', 'is', 'a', 'test.', 'Foo', 'Bar', true, false, 123]
];

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'example.yaml');

$Object = new \Maikuolan\Common\YAML($RawYAML);
if ($Expected !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object = new \Maikuolan\Common\YAML();
$Object->Refs = &$Object->Data;
$ProcessResult = $Object->process($RawYAML, $Object->Data);
$ExitCode++;
if ($ProcessResult !== true) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if ($Expected !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$InvalidYAML = 1000;
$NoNewLineYAML = "No new end of line";
$Object = new \Maikuolan\Common\YAML();
$ExitCode++;
if ($Object->process($InvalidYAML, $Object->Data) !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExitCode++;
if ($Object->process($NoNewLineYAML, $Object->Data) !== false) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
