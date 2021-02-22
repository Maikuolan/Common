<?php
/**
 * L10N handler tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$DataEN = [
    'IntegerRule' => 'int2Type4',
    'FractionRule' => 'int1',
    'MyName' => 'Hello! My name is %s.',
    'YourName' => 'What is your name?',
    'DoYouSpeak' => 'Do you speak English?'
];

$DataFR = [
    'IntegerRule' => 'int2Type3',
    'FractionRule' => 'fraction2Type1',
    'MyName' => 'Bonjour ! Je m\'appelle %s.',
    'YourName' => 'Quel est votre nom ?'
];

$L10N = new \Maikuolan\Common\L10N($DataFR, $DataEN);

if ('Bonjour ! Je m\'appelle Mary Sue.' !== sprintf($L10N->getString('MyName'), 'Mary Sue')) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
$ExitCode++;
if ('Quel est votre nom ?' !== $L10N->getString('YourName')) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
$ExitCode++;
if ('Do you speak English?' !== $L10N->getString('DoYouSpeak')) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$DataEN = [
    'IntegerRule' => 'int2Type4',
    'FractionRule' => 'int1',
    'apples' => [
        'There is %s apple on the tree.',
        'There are %s apples on the tree.'
    ],
    'oranges' => [
        'There is %s orange on the tree.',
        'There are %s oranges on the tree.'
    ],
];

$DataRU = [
    'IntegerRule' => 'int3Type4',
    'FractionRule' => 'int1',
    'apples' => [
        'На дереве есть %s яблоко.',
        'На дереве есть %s яблока.',
        'На дереве есть %s яблок.'
    ]
];

$L10N = new \Maikuolan\Common\L10N($DataRU, $DataEN);

$ExpectedRU = [
    'На дереве есть 0 яблок.',
    'На дереве есть 1 яблоко.',
    'На дереве есть 2 яблока.',
    'На дереве есть 3 яблока.',
    'На дереве есть 4 яблока.',
    'На дереве есть 5 яблок.',
];

$ExpectedEN = [
    'There are 0 oranges on the tree.',
    'There is 1 orange on the tree.',
    'There are 2 oranges on the tree.',
    'There are 3 oranges on the tree.',
    'There are 4 oranges on the tree.',
    'There are 5 oranges on the tree.',
];

foreach (range(0, 5) as $Number) {
    $ExitCode++;
    if ($ExpectedRU[$Number] !== sprintf($L10N->getPlural($Number, 'apples'), $Number)) {
        echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
        exit($ExitCode);
    }
}

foreach (range(0, 5) as $Number) {
    $ExitCode++;
    if ($ExpectedEN[$Number] !== sprintf($L10N->getPlural($Number, 'oranges'), $Number)) {
        echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
        exit($ExitCode);
    }
}
