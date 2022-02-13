<?php
/**
 * YAML handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * @link https://github.com/Maikuolan/Common
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
        'They said, "Our number is #123-456-789".',
        'こんにちは世界。 こんにちは世界。 こんにちは世界。 こんにちは世界。',
        'مرحبا بالعالم. مرحبا بالعالم.',
        '你好世界。 你好世界。 你好世界。 你好世界。'
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
        ]
    ],
    'Flattened array test 1' => [
        0 => 'Foo',
        1 => 'Bar',
        'Hello World' => 'Hello to the world! :-)',
        'Goodbye world' => 'Goodbye cruel world. :-(',
        'Oh hi there' => 'Oh hi there! ;-)',
        2 => 'What\'s up buddy',
        3 => 'How\'re you'
    ],
    'Flattened array test 2' => [
        'Somewhere to start' => 'Somewhere to go',
        0 => 'Foo',
        1 => 'Bar',
        'Hello World' => 'Hello to the world! :-)',
        'Goodbye world' => 'Goodbye cruel world. :-(',
        'Oh hi there' => 'Oh hi there! ;-)',
        2 => 'What\'s up buddy',
        3 => 'How\'re you',
        'Lorem ipsum' => 'Dolor sit amet, consectetur adipiscing elit.',
        'Sed do eiusmod tempor' => 'Incididunt ut labore et dolore magna aliqua.'
    ],
    'Test support for PHP functions in YAML' => [
        'String functions' => [
            'Test addslashes' => '\\\'\\"<>&',
            'Test bin2hex' => '48656c6c6f20776f726c642e',
            'Test hex2bin' => 'Hello world.',
            'Test html_entity_decode' => '\'"<>&',
            'Test htmlentities' => '&#039;&quot;&lt;&gt;&amp;',
            'Test htmlspecialchars' => '&#039;&quot;&lt;&gt;&amp;',
            'Test htmlspecialchars_decode' => '\'"<>&',
            'Test lcfirst' => 'hELLO WORLD.',
            'Test nl2br' => "Hello<br />\nworld.",
            'Test ord' => 72,
            'Test quotemeta' => '\\.\\\\\\+\\*\\?\\[\\^\\]\\(\\$\\)',
            'Test str_rot13' => 'Uryyb jbeyq.',
            'Test strip_tags' => 'Hello world.',
            'Test stripslashes' => 'Hello world.',
            'Test stripcslashes' => 'Hello world.',
            'Test strlen' => 12,
            'Test strrev' => '.dlrow olleH',
            'Test strtolower' => 'hello world.',
            'Test strtoupper' => 'HELLO WORLD.',
            'Test ucfirst' => 'Hello world.',
            'Test ucwords' => 'Hello World.'
        ],
        'Numeric functions' => [
            'Default number' => 123456789.87654321,
            'Test abs' => 123456789.87654321,
            'Test acos' => NAN,
            'Test acosh' => 19.324548953827964,
            'Test asin' => NAN,
            'Test asinh' => 19.324548953827964,
            'Test atan' => 1.5707963186948966,
            'Test atanh' => NAN,
            'Test ceil' => 123456790.0,
            'Test chr' => "\x15",
            'Test cos' => -0.6711948791807985,
            'Test cosh' => INF,
            'Test decbin' => '111010110111100110100010101',
            'Test dechex' => '75bcd15',
            'Test decoct' => '726746425',
            'Test deg2rad' => 2154727.467288483,
            'Test exp' => INF,
            'Test expm1' => INF,
            'Test floor' => 123456789.0,
            'Test log10' => 8.09151498025276,
            'Test log1p' => 18.63140178136802,
            'Test rad2deg' => 7073553012.159353,
            'Test round' => 123456790.0,
            'Test sin' => 0.7412809414530185,
            'Test sinh' => INF,
            'Test tan' => -1.1044198405651737,
            'Test tanh' => 1.0,
            'Test sqrt' => 11111.1111
        ],
        'Hashes' => [
            'Test MD2' => '9a23cfa6aae6635b88d8d2ee28b23bc8',
            'Test MD5' => '764569e58f53ea8b6404f6fa7fc0247f',
            'Test SHA1' => 'e44f3364019d18a151cab7072b5a40bb5b3e274f',
            'Test SHA256' => 'aa3ec16e6acc809d8b2818662276256abfd2f1b441cb51574933f3d4bd115d11',
            'Test SHA512' => '70f460361a639767d665c14727b2f18bed18c8c6be6a6ad3950e976167ba57a8db214ac3ded3d7777e5eb20ea61a2f8a24d026d285cab4ba4d38dc1c410136f7',
            'Test Whirlpool' => 'b8b3eabbf098df12bd3430d4bd214d0cffafb2111e70da8e0315be9e81ecdfb2f64c61b5348ad46522a1094093982491e2f384a03fd4f9c18a055ddbe929f345'
        ]
    ],
    'Specification examples' => [
        '2.1. Collections' => [
            '2.1 Sequence of Scalars' => ['Mark McGwire', 'Sammy Sosa', 'Ken Griffey'],
            '2.2 Mapping Scalars to Scalars' => [
                'hr' => 65,
                'avg' => 0.278,
                'rbi' => 147
            ],
            '2.3 Mapping Scalars to Sequences' => [
                'american' => ['Boston Red Sox', 'Detroit Tigers', 'New York Yankees'],
                'national' => ['New York Mets', 'Chicago Cubs', 'Atlanta Braves']
            ],
            '2.4 Sequence of Mappings' => [[
                'name' => 'Mark McGwire',
                'hr' => 65,
                'avg' => 0.278
            ], [
                'name' => 'Sammy Sosa',
                'hr' => 63,
                'avg' => 0.288
            ]],
            '2.5 Sequence of Sequences' => [
                ['name', 'hr', 'avg'],
                ['Mark McGwire', 65, 0.278],
                ['Sammy Sosa', 63, 0.288]
            ]
        ],
        '2.3. Scalars' => [
            '2.16 Indentation determines scope' => [
                'name' => 'Mark McGwire',
                'accomplishment' => 'Mark set a major league home run record in 1998.',
                'stats' => "65 Home Runs\n0.278 Batting Average"
            ]
        ]
    ],
    'End of file' => ':-)'
];

$ExpectedForSyntaxSerialised = serialize($ExpectedForSyntax);

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

$ExpectedForReconstructionSerialised = serialize($ExpectedForReconstruction);

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'syntax.yaml');

$Object = new \Maikuolan\Common\YAML($RawYAML);
if ($ExpectedForSyntaxSerialised !== serialize($Object->Data)) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($ExpectedForSyntax);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
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
if ($ExpectedForSyntaxSerialised !== serialize($Object->Data)) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($ExpectedForSyntax);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
    exit($ExitCode);
}

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'reconstruct.yaml');

$Object = new \Maikuolan\Common\YAML($RawYAML);
$ExitCode++;
if ($ExpectedForReconstructionSerialised !== serialize($Object->Data)) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($ExpectedForReconstruction);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
    exit($ExitCode);
}

$Reconstructed = $Object->reconstruct($Object->Data, true, true);
$ExitCode++;
if ($RawYAML !== $Reconstructed) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($Reconstructed);
    echo PHP_EOL . 'Actual: ';
    var_dump($RawYAML);
    echo PHP_EOL;
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

$ExpectedForUTF16Serialised = serialize($ExpectedForUTF16);

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'utf16be.yaml');
$Object = new \Maikuolan\Common\YAML($RawYAML);
$ExitCode++;
if ($ExpectedForUTF16Serialised !== serialize($Object->Data)) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($ExpectedForUTF16);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
    exit($ExitCode);
}

$RawYAML = file_get_contents($TestsDir . 'fixtures' . DIRECTORY_SEPARATOR . 'utf16le.yaml');
$Object = new \Maikuolan\Common\YAML($RawYAML);
$ExitCode++;
if ($ExpectedForUTF16Serialised !== serialize($Object->Data)) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($ExpectedForUTF16);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
    exit($ExitCode);
}

$RawJSON = file_get_contents($BaseDir . 'composer.json');
$PHPDecoded = json_decode($RawJSON, true);
$PHPDecodedSerialised = serialize($PHPDecoded);
$Object = new \Maikuolan\Common\YAML($RawJSON);
$ExitCode++;
if ($PHPDecodedSerialised !== serialize($Object->Data)) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL . 'Expected: ';
    var_dump($PHPDecoded);
    echo PHP_EOL . 'Actual: ';
    var_dump($Object->Data);
    echo PHP_EOL;
    exit($ExitCode);
}
