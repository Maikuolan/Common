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
    'Example null set' => [
        'Bar0' => null,
        'Bar1' => null,
        'Bar2' => null,
        'Bar3' => null
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
    'Hexadecimal number notation' => 65536,
    'Binary number notation' => 16,
    'Octal number notation' => 4096,
    'Example explicit tags (type coercion)' => [
        'Normal string' => '123 Hello',
        'Make the string a bool' => true,
        'Make the string a float' => 123.0,
        'Make the string a null set' => ['123 Hello' => null],
        'Make the string a null' => null,
        'Make the string a numeric array' => ['123 Hello'],
        'Make the string an integer' => 123,
        'Make the string an integer from an anchor' => 123,
        'Normal integer' => 12345,
        'Make the integer a bool' => true,
        'Make the integer a float' => 12345.0,
        'Make the integer a null set' => [12345 => null],
        'Make the integer a null' => null,
        'Make the integer a numeric array' => [12345],
        'Make the integer a string' => '12345',
        'Make the integer a string from an anchor' => '12345',
        'Normal float' => 123.456,
        'Make the float a bool' => true,
        'Make the float a null' => null,
        'Make the float a string' => '123.456',
        'Make the float an integer' => 123,
        'Make the float an integer from an anchor' => 123,
        'Normal array' => ['Foo', 'Bar', 1],
        'Make the array a bool' => true,
        'Make the array a float' => 3.0,
        'Make the array a null set' => ['Foo' => null, 'Bar' => null, 1 => null],
        'Make the array a null' => null,
        'Make the array a numeric array' => ['Foo', 'Bar', 1],
        'Make the array an integer' => 3,
        'Make the array an integer from an anchor' => 3,
        'To bool' => [false, true, true, false, false, true, true, false, true, false],
        'To int' => [0, 1, 0, 0, 0, 1],
        'To string' => ['null', 'true', 'false']
    ],
    'Binary single-line example' => 'Hello world! :-)',
    'Binary multi-line example' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    'Make the array into a null set' => ['Bar' => null, 'Baz' => null, 'Boo' => null],
    'Make the array sequential (numeric)' => ['Bar', 'Baz', 'Boo'],
    'Example flow mapping' => [
        'Foo' => 'Bar',
        'Foz' => 'Baz',
        'Far' => 'Boo'
    ],
    'Test ability to merge' => [
        'Foo' => 'Bar1',
        'Foz' => 'Baz1',
        'Far' => 'Boo1',
        'Moz' => 'Baz2',
        'Mar' => 'Boo2',
        'Lorem' => 'Ipsum',
        'Woo' => 'Bar3',
        'Hi there' => 'How are you',
        'What are you doing' => 'Reading a YAML file'
    ],
    'Blocky foo' => 'Bar1',
    'Blocky foz' => 'Baz1',
    'Blocky far' => 'Boo1',
    'Blocky lorem' => 'Blocky ipsum',
    'Blocky hello' => 'How are you',
    'What is happening' => 'Reading a YAML file',
    'Mixed flow style test 1' => ['Foo', 'Bar', [
        'Hello World' => 'Hello to the world! :-)',
        'Goodbye world' => 'Goodbye cruel world. :-(',
        'Oh hi there' => 'Oh hi there! ;-)'
    ], 'What\'s up buddy'],
    'Mixed flow style test 2' => [
        'Foo and bar' => ['Foo', 'Bar'],
        'Hellos and goodbyes' => [
            'Hello World' => 'Hello to the world! :-)',
            'Goodbye world' => 'Goodbye cruel world. :-(',
            'Oh hi there' => 'Oh hi there! ;-)'
        ],
        'Deeper' => [
            'And deeper' => [
                'And deepest' => ['What\'s up buddy', 'How\'re you']
            ]
        ],
    ],
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
    'Example null set' => [
        'Bar0' => null,
        'Bar1' => null,
        'Bar2' => null,
        'Bar3' => null
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
    'Hexadecimal number notation' => 65536,
    'Binary number notation' => 16,
    'Octal number notation' => 4096,
    'Example implicit numeric array' => ['Bar0', 'Bar1', 'Bar2', 'Bar3'],
    'Example associative array' => [
        'Foo1' => 'Bar1',
        'Foo2' => 'Bar2',
        'Foo3' => 'Bar3',
        'Foo4' => 'Bar4'
    ],
    'Example null set' => [
        'Bar0' => null,
        'Bar1' => null,
        'Bar2' => null,
        'Bar3' => null
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

$RawJSON = file_get_contents($BaseDir . 'composer.json');
$PHPDecoded = json_decode($RawJSON, true);
$Object = new \Maikuolan\Common\YAML($RawJSON);
$ExitCode++;
if ($PHPDecoded !== $Object->Data) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
