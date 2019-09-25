<?php

namespace Maikuolan\Common\Tests;

use PHPUnit\Framework\TestCase;
use Maikuolan\Common\YAML;

class YAMLTest extends TestCase
{
    public function testProcessDataWithInstantiation()
    {
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

        $RawYAML = file_get_contents(__DIR__ . '/fixtures/example.yaml');

        $Object = new YAML($RawYAML);

        $this->assertSame($Expected, $Object->Data);
    }

    public function testProcessWithValidYAML()
    {
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

        $RawYAML = file_get_contents(__DIR__ . '/fixtures/example.yaml');

        $Object = new YAML();

        $ProcessResult = $Object->process($RawYAML, $Object->Data);

        $this->assertTrue($ProcessResult);
        $this->assertSame($Expected, $Object->Data);
    }

    public function testProcessWithInvalidYAML()
    {
        $InvalidYAML = 1000;

        $NoNewLineYAML = "No new end of line";

        $Object = new YAML();

        $this->assertFalse($Object->process($InvalidYAML, $Object->Data));
        $this->assertFalse($Object->process($NoNewLineYAML, $Object->Data));
    }
}
