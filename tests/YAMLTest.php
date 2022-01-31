<?php
/**
 * YAML handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$ExpectedForSyntax = [
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
    'Example implicit numeric array' => ['Bar0', 'Bar1', 'Bar2', 'Bar3'],
    'Example explicit numeric array' => ['Bar0', 'Bar1', 'Bar2', 'Bar3'],
    'Example associative array' => [
        'Foo1' => 'Bar1',
        'Foo2' => 'Bar2',
        'Foo3' => 'Bar3',
        'Foo4' => 'Bar4'
    ],
    'Example mixed multi-dimensional array' => [
        0 => 'Bar0',
        1 => 'Bar1',
        2 => 'Bar2',
        3 => 'Bar3',
        'xFooX' => 'xBarX',
        'Some int' => 4567,
        'Sub array' => [
            'Hello' => 'World',
            'Sub-sub array' => [
                'Foobar' => 'Barfoo'
            ]
        ]
    ],
    'Hex-encoded string example' => "Hello World (but in hex)\0",
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
    'Testing anchors' => [
        'Anchored text push' => 'Some placeholder text.',
        'Anchored text pull' => 'Some placeholder text.'
    ],
    'Escaping test' => [
        'They said, \"Our number is #123-456-789\".',
        'こんにちは世界。 \xE3\x81\x93\xE3\x82\x93\xE3\x81\xAB\xE3\x81\xA1\xE3\x81\xAF\xE4\xB8\x96\xE7\x95\x8C\xE3\x80\x82 \u3053\u3093\u306B\u3061\u306F\u4E16\u754C\u3002 \U00003053\U00003093\U0000306B\U00003061\U0000306F\U00004E16\U0000754C\U00003002',
        'مرحبا بالعالم. \xD9\x85\xD8\xB1\xD8\xAD\xD8\xA8\xD8\xA7 \xD8\xA8\xD8\xA7\xD9\x84\xD8\xB9\xD8\xA7\xD9\x84\xD9\x85.',
        '你好世界。 \xE4\xBD\xA0\xE5\xA5\xBD\xE4\xB8\x96\xE7\x95\x8C\xE3\x80\x82 \u4F60\u597D\u4E16\u754C\u3002 \U00004F60\U0000597D\U00004E16\U0000754C\U00003002',
    ],
    'Inserts test' => 'Hello world; Thus, from here, within this variable, a value is inserted; It should work, hopefully.',
    'Inline array example' => ['this', 'is', 'a', 'test.', 'Foo', 'Bar', true, false, 123],
    'End of file' => ':-)'
];

$ExpectedForReconstruction = [
    'String foo' => 'Bar',
    'Integer foo' => 1234,
    'Float foo' => 123.4,
    'Example implicit numeric array' => ['Bar0', 'Bar1', 'Bar2', 'Bar3'],
    'Example associative array' => [
        'Foo1' => 'Bar1',
        'Foo2' => 'Bar2',
        'Foo3' => 'Bar3',
        'Foo4' => 'Bar4'
    ],
    'Example mixed multi-dimensional array' => [
        0 => 'Bar0',
        1 => 'Bar1',
        'xFooX' => 'xBarX',
        'Some int' => 4567,
        'Sub array' => [
            'Hello' => 'World',
            'Sub-sub array' => [
                'Foobar' => 'Barfoo'
            ]
        ]
    ],
    'Multi-line example' => "h e l l o - w o r l d\nhello-world",
    'Example booleans and null' => [
        'This is true' => true,
        'This is false' => false,
        'This is null' => null
    ],
    'Testing anchors' => [
        'Anchored text push' => 'Some placeholder text.',
        'Anchored text pull' => 'Some placeholder text.'
    ],
    'Escaping test' => 'Our number is #123-456-789.',
    'End of file' => ':-)'
];

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'syntax.yaml');

$Object = new \Maikuolan\Common\YAML($RawYAML);
if ($ExpectedForSyntax !== $Object->Data) {
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
if ($ExpectedForSyntax !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Object->Data = [];
$Object->EscapeBySpec = true;
$ProcessResult = $Object->process($RawYAML, $Object->Data);
$ExitCode++;
if ($ProcessResult !== true) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExpectedForBySpecEscaped = $ExpectedForSyntax;
$ExpectedForBySpecEscaped['Escaping test'] = [
    'They said, "Our number is #123-456-789".',
    'こんにちは世界。 こんにちは世界。 こんにちは世界。 こんにちは世界。',
    'مرحبا بالعالم. مرحبا بالعالم.',
    '你好世界。 你好世界。 你好世界。 你好世界。'
];
$ExitCode++;
if ($ExpectedForBySpecEscaped !== $Object->Data) {
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

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'reconstruct.yaml');

$Object = new \Maikuolan\Common\YAML($RawYAML);
$ExitCode++;
if ($ExpectedForReconstruction !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Reconstructed = $Object->reconstruct($Object->Data, true, true);
$ExitCode++;
if ($Reconstructed !== $RawYAML) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$ExpectedForUTF16 = [
    'String foo' => 'Bar',
    'Integer foo' => 1234,
    'Float foo' => 123.4,
    'Example implicit numeric array' => ['Bar0', 'Bar1', 'Bar2', 'Bar3'],
    'Example associative array' => [
        'Foo1' => 'Bar1',
        'Foo2' => 'Bar2',
        'Foo3' => 'Bar3',
        'Foo4' => 'Bar4'
    ]
];

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'utf16be.yaml');
$Object = new \Maikuolan\Common\YAML($RawYAML);
$ExitCode++;
if ($ExpectedForUTF16 !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'utf16le.yaml');
$Object = new \Maikuolan\Common\YAML($RawYAML);
$ExitCode++;
if ($ExpectedForUTF16 !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
