<?php
/**
 * Operation handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * @link https://github.com/Maikuolan/Common
 */

/**
 * If this file remains intact after deploying the package to production,
 * preventing it from running outside of Composer may be useful as a means of
 * prevent potential attackers from hammering the file and needlessly wasting
 * cycles at the server.
 */
if (!isset($_SERVER['COMPOSER_BINARY'])) {
    die;
}

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

// Try to not look too deeply into my choice of data to be included here,
// excluded, or which details are connected. It's generally arbitrary, chosen
// merely for the sake of having something simple, concise, but not overly
// large to be able to perform some tests against, to ensure that the operation
// handler's ifCompare method is working as intended. That's it.
$TestData = [
    'AU' => [
        'Country' => 'Australia',
        'Region' => 'Oceania',
        'British Empire 1921' => true
    ],
    'CA' => [
        'Country' => 'Canada',
        'Region' => 'North America',
        'British Empire 1921' => true
    ],
    'CN' => [
        'Country' => 'China',
        'Region' => 'Asia',
        'British Empire 1921' => false
    ],
    'DE' => [
        'Country' => 'Germany',
        'Region' => 'Europe',
        'British Empire 1921' => false
    ],
    'ES' => [
        'Country' => 'Spain',
        'Region' => 'Europe',
        'British Empire 1921' => false
    ],
    'FR' => [
        'Country' => 'France',
        'Region' => 'Europe',
        'British Empire 1921' => false
    ],
    'IN' => [
        'Country' => 'India',
        'Region' => 'Asia',
        'British Empire 1921' => true
    ],
    'JP' => [
        'Country' => 'Japan',
        'Region' => 'Asia',
        'British Empire 1921' => false
    ],
    'NZ' => [
        'Country' => 'New Zealand',
        'Region' => 'Oceania',
        'British Empire 1921' => true
    ],
    'PT' => [
        'Country' => 'Portugal',
        'Region' => 'Europe',
        'British Empire 1921' => false
    ],
    'US' => [
        'Country' => 'the United States',
        'Region' => 'North America',
        'British Empire 1921' => false
    ],
    'VN' => [
        'Country' => 'Vietnam',
        'Region' => 'Asia',
        'British Empire 1921' => false
    ]
];
$Keys = array_keys($TestData);
$Expected = 'In 1921, Australia WAS a part of the British Empire.
Australia and Canada belong to different regions.
Australia and China belong to different regions.
Australia and Germany belong to different regions.
Australia and Spain belong to different regions.
Australia and France belong to different regions.
Australia and India belong to different regions.
Australia and Japan belong to different regions.
Australia and New Zealand belong to the same region: Oceania.
Australia and Portugal belong to different regions.
Australia and the United States belong to different regions.
Australia and Vietnam belong to different regions.
In 1921, Canada WAS a part of the British Empire.
Canada and Australia belong to different regions.
Canada and China belong to different regions.
Canada and Germany belong to different regions.
Canada and Spain belong to different regions.
Canada and France belong to different regions.
Canada and India belong to different regions.
Canada and Japan belong to different regions.
Canada and New Zealand belong to different regions.
Canada and Portugal belong to different regions.
Canada and the United States belong to the same region: North America.
Canada and Vietnam belong to different regions.
In 1921, China was NOT a part of the British Empire.
China and Australia belong to different regions.
China and Canada belong to different regions.
China and Germany belong to different regions.
China and Spain belong to different regions.
China and France belong to different regions.
China and India belong to the same region: Asia.
China and Japan belong to the same region: Asia.
China and New Zealand belong to different regions.
China and Portugal belong to different regions.
China and the United States belong to different regions.
China and Vietnam belong to the same region: Asia.
In 1921, Germany was NOT a part of the British Empire.
Germany and Australia belong to different regions.
Germany and Canada belong to different regions.
Germany and China belong to different regions.
Germany and Spain belong to the same region: Europe.
Germany and France belong to the same region: Europe.
Germany and India belong to different regions.
Germany and Japan belong to different regions.
Germany and New Zealand belong to different regions.
Germany and Portugal belong to the same region: Europe.
Germany and the United States belong to different regions.
Germany and Vietnam belong to different regions.
In 1921, Spain was NOT a part of the British Empire.
Spain and Australia belong to different regions.
Spain and Canada belong to different regions.
Spain and China belong to different regions.
Spain and Germany belong to the same region: Europe.
Spain and France belong to the same region: Europe.
Spain and India belong to different regions.
Spain and Japan belong to different regions.
Spain and New Zealand belong to different regions.
Spain and Portugal belong to the same region: Europe.
Spain and the United States belong to different regions.
Spain and Vietnam belong to different regions.
In 1921, France was NOT a part of the British Empire.
France and Australia belong to different regions.
France and Canada belong to different regions.
France and China belong to different regions.
France and Germany belong to the same region: Europe.
France and Spain belong to the same region: Europe.
France and India belong to different regions.
France and Japan belong to different regions.
France and New Zealand belong to different regions.
France and Portugal belong to the same region: Europe.
France and the United States belong to different regions.
France and Vietnam belong to different regions.
In 1921, India WAS a part of the British Empire.
India and Australia belong to different regions.
India and Canada belong to different regions.
India and China belong to the same region: Asia.
India and Germany belong to different regions.
India and Spain belong to different regions.
India and France belong to different regions.
India and Japan belong to the same region: Asia.
India and New Zealand belong to different regions.
India and Portugal belong to different regions.
India and the United States belong to different regions.
India and Vietnam belong to the same region: Asia.
In 1921, Japan was NOT a part of the British Empire.
Japan and Australia belong to different regions.
Japan and Canada belong to different regions.
Japan and China belong to the same region: Asia.
Japan and Germany belong to different regions.
Japan and Spain belong to different regions.
Japan and France belong to different regions.
Japan and India belong to the same region: Asia.
Japan and New Zealand belong to different regions.
Japan and Portugal belong to different regions.
Japan and the United States belong to different regions.
Japan and Vietnam belong to the same region: Asia.
In 1921, New Zealand WAS a part of the British Empire.
New Zealand and Australia belong to the same region: Oceania.
New Zealand and Canada belong to different regions.
New Zealand and China belong to different regions.
New Zealand and Germany belong to different regions.
New Zealand and Spain belong to different regions.
New Zealand and France belong to different regions.
New Zealand and India belong to different regions.
New Zealand and Japan belong to different regions.
New Zealand and Portugal belong to different regions.
New Zealand and the United States belong to different regions.
New Zealand and Vietnam belong to different regions.
In 1921, Portugal was NOT a part of the British Empire.
Portugal and Australia belong to different regions.
Portugal and Canada belong to different regions.
Portugal and China belong to different regions.
Portugal and Germany belong to the same region: Europe.
Portugal and Spain belong to the same region: Europe.
Portugal and France belong to the same region: Europe.
Portugal and India belong to different regions.
Portugal and Japan belong to different regions.
Portugal and New Zealand belong to different regions.
Portugal and the United States belong to different regions.
Portugal and Vietnam belong to different regions.
In 1921, the United States was NOT a part of the British Empire.
The United States and Australia belong to different regions.
The United States and Canada belong to the same region: North America.
The United States and China belong to different regions.
The United States and Germany belong to different regions.
The United States and Spain belong to different regions.
The United States and France belong to different regions.
The United States and India belong to different regions.
The United States and Japan belong to different regions.
The United States and New Zealand belong to different regions.
The United States and Portugal belong to different regions.
The United States and Vietnam belong to different regions.
In 1921, Vietnam was NOT a part of the British Empire.
Vietnam and Australia belong to different regions.
Vietnam and Canada belong to different regions.
Vietnam and China belong to the same region: Asia.
Vietnam and Germany belong to different regions.
Vietnam and Spain belong to different regions.
Vietnam and France belong to different regions.
Vietnam and India belong to the same region: Asia.
Vietnam and Japan belong to the same region: Asia.
Vietnam and New Zealand belong to different regions.
Vietnam and Portugal belong to different regions.
Vietnam and the United States belong to different regions.
';
$Out = '';
foreach ($Keys as $KeyA) {
    $Out .= 'In 1921, ' . $Object->dataTraverse($TestData, $KeyA . '.Country') . ' ' . $Object->ifCompare($TestData, 'if {' . $KeyA . '.British Empire 1921}==={AU.British Empire 1921} then WAS else was NOT') . " a part of the British Empire.\n";
    foreach ($Keys as $KeyB) {
        if ($KeyA === $KeyB) {
            continue;
        }
        $Try = $Object->ifCompare($TestData, 'if {' . $KeyA . '.Region}==={' . $KeyB . '.Region} then {' . $KeyA . '.Region} else Different');
        $Out .= ucfirst($Object->dataTraverse($TestData, $KeyA . '.Country')) . ' and ' . $Object->dataTraverse($TestData, $KeyB . '.Country') . ' belong to ' . ($Try === 'Different' ? 'different regions.' : 'the same region: ' . $Try . '.') . "\n";
    }
}

echo $Out;

$ExitCode++;
if ($Out !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

// singleCompare examples from the documentation.
$Expected = [true, false, true, true, true, false, false, true, true];

$Out = [
    $Object->singleCompare('1.2.3', '^1'),
    $Object->singleCompare('1.2.3', '^2'),
    $Object->singleCompare('1.2.3', '>=1 <2'),
    $Object->singleCompare('2.3.4', '>=2.3 <4'),
    $Object->singleCompare('3.4.5', '^1|^3'),
    $Object->singleCompare('4.5.6', '<4'),
    $Object->singleCompare('4.5.6', '<=4'),
    $Object->singleCompare('4.5.6', '<=5'),
    $Object->singleCompare('4.5.6', '4.5.6')
];

$ExitCode++;
if ($Out !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

// dataTraverse examples from the documentation.
$TestData = [
    'Foo' => [
        'Bar' => ['Hello' => 'World', 'Goodbye' => 'Cruel World'],
        'Baz' => 'Hello World!'
    ],
    'Far' => ['Boo' => 'To You'],
    'Plenty of space' => '   ...yep.'
];

$Expected = ['World', 'Cruel World', 11, 'Hello World!', 'HELLO WORLD!', 'hello world!', '   ...yep.', '...yep.', '', 'World'];

$Out = [
    $Object->dataTraverse($TestData, 'Foo.Bar.Hello'),
    $Object->dataTraverse($TestData, 'Foo.Bar.Goodbye'),
    $Object->dataTraverse($TestData, 'Foo.Bar.Goodbye.strlen()'),
    $Object->dataTraverse($TestData, 'Foo.Baz'),
    $Object->dataTraverse($TestData, 'Foo.Baz.strtoupper()'),
    $Object->dataTraverse($TestData, 'Foo.Baz.strtolower()'),
    $Object->dataTraverse($TestData, 'Plenty of space'),
    $Object->dataTraverse($TestData, 'Plenty of space.trim()'),
    $Object->dataTraverse($TestData, 'This element does not exist'),
    $Object->dataTraverse($TestData, 'Foo.Bar.Hello.Element is not an array')
];

$ExitCode++;
if ($Out !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

// ifCompare examples from the documentation.
$TestData = [
    'Foo' => ['Bar' => 'Hello', 'Baz' => 'Goodbye'],
    'Numbers' => ['One' => 1, 'Ten' => 10, 'Hundred' => 100],
    'Versions' => ['First' => '1.2.3', 'Second' => '2.3.4']
];

$Expected = ['Hello', 'Success', 'Failure', 'Yeah, sounds right.', 'Sayonara', 'They are different'];

$Out = [
    $Object->ifCompare($TestData, 'if {Versions.Second}^2.3 then {Foo.Bar} else {Foo.Baz}'),
    $Object->ifCompare($TestData, 'if {Versions.Second}^2.3 thenif {Versions.First}^1.2 then Success else Failure'),
    $Object->ifCompare($TestData, 'if {Versions.Second}^1.2 thenif {Versions.First}^3.4 then Success else Failure'),
    $Object->ifCompare($TestData, 'if 1>2 then WTH? else Yeah, sounds right.'),
    $Object->ifCompare($TestData, 'if {Foo.Baz}===Goodbye then Sayonara else Ohayogozaimasu'),
    $Object->ifCompare($TestData, 'if {Versions.Second}==={Versions.First} then They are the same else They are different')
];

$ExitCode++;
if ($Out !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
