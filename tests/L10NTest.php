<?php

namespace Maikuolan\Common\Tests;

use PHPUnit\Framework\TestCase;
use Maikuolan\Common\L10N;

class L10NTest extends TestCase
{
    public function testConstructor()
    {
        $data = [
            'IntegerRule' => 'int2Type1',
        ];

        $fallback = [
            'IntegerRule' => 'int2Type1',
        ];

        $l10N = new L10N($data, $fallback);

        $this->assertInstanceOf('\Maikuolan\Common\L10N', $l10N);
    }

    public function pluralDataProviderWithData()
    {
        return [
            ['IntegerRule', 'int1', 'int1'],
            ['IntegerRule', 'int2Type1', 'int2Type1'],
            ['IntegerRule', 'int2Type2', 'int2Type2'],
            ['IntegerRule', 'int2Type3', 'int2Type3'],
            ['IntegerRule', 'int2Type4', 'int2Type4'],
            ['IntegerRule', 'int3Type1', 'int3Type1'],
            ['IntegerRule', 'int3Type2', 'int3Type2'],
            ['IntegerRule', 'int3Type3', 'int3Type3'],
            ['IntegerRule', 'int3Type4', 'int3Type4'],
            ['IntegerRule', 'int3Type5', 'int3Type5'],
            ['IntegerRule', 'int3Type6', 'int3Type6'],
            ['IntegerRule', 'int3Type7', 'int3Type7'],
            ['IntegerRule', 'int3Type8', 'int3Type8'],
            ['IntegerRule', 'int3Type9', 'int3Type9'],
            ['IntegerRule', 'int4Type1', 'int4Type1'],
            ['IntegerRule', 'int4Type2', 'int4Type2'],
            ['IntegerRule', 'int4Type3', 'int4Type3'],
            ['IntegerRule', 'int4Type4', 'int4Type4'],
            ['IntegerRule', 'int4Type5', 'int4Type5'],
            ['IntegerRule', 'int4Type6', 'int4Type6'],
            ['IntegerRule', 'int5Type1', 'int5Type1'],
            ['IntegerRule', 'int6Type1', 'int6Type1'],
            ['IntegerRule', 'int6Type2', 'int6Type2'],
            ['FractionRule', 'fraction2Type1', 'fraction2Type1'],
            ['FractionRule', 'fraction2Type2', 'fraction2Type2'],
        ];
    }

    /**
     * @dataProvider pluralDataProviderWithData
     */
    public function testGetPluralWithExistedDataString($number, $string, $expected)
    {
        $data = [
            $number => $string,
        ];

        $l10N = new L10N($data);

        $this->assertSame($expected, $l10N->getPlural(1, $number));
    }

    public function pluralDataProviderWithFallback()
    {
        return [
            ['IntegerRule', 'int1', 'int1'],
            ['IntegerRule', 'int2Type1', 'int2Type1'],
            ['IntegerRule', 'int2Type2', 'int2Type2'],
            ['IntegerRule', 'int2Type3', 'int2Type3'],
            ['IntegerRule', 'int2Type4', 'int2Type4'],
            ['IntegerRule', 'int3Type1', 'int3Type1'],
            ['IntegerRule', 'int3Type2', 'int3Type2'],
            ['IntegerRule', 'int3Type3', 'int3Type3'],
            ['IntegerRule', 'int3Type4', 'int3Type4'],
            ['IntegerRule', 'int3Type5', 'int3Type5'],
            ['IntegerRule', 'int3Type6', 'int3Type6'],
            ['IntegerRule', 'int3Type7', 'int3Type7'],
            ['IntegerRule', 'int3Type8', 'int3Type8'],
            ['IntegerRule', 'int3Type9', 'int3Type9'],
            ['IntegerRule', 'int4Type1', 'int4Type1'],
            ['IntegerRule', 'int4Type2', 'int4Type2'],
            ['IntegerRule', 'int4Type3', 'int4Type3'],
            ['IntegerRule', 'int4Type4', 'int4Type4'],
            ['IntegerRule', 'int4Type5', 'int4Type5'],
            ['IntegerRule', 'int4Type6', 'int4Type6'],
            ['IntegerRule', 'int5Type1', 'int5Type1'],
            ['IntegerRule', 'int6Type1', 'int6Type1'],
            ['IntegerRule', 'int6Type2', 'int6Type2'],
            ['FractionRule', 'fraction2Type1', 'fraction2Type1'],
            ['FractionRule', 'fraction2Type2', 'fraction2Type2'],
        ];
    }

    /**
     * @dataProvider pluralDataProviderWithFallback
     */
    public function testGetPluralWithExistedFallbackString($number, $string, $expected)
    {
        $data = [];

        $fallback = [
            $number => $string,
        ];

        $l10N = new L10N($data, $fallback);

        $this->assertSame($expected, $l10N->getPlural(1, $number));
    }

    public function testGetPluralWithEmptyDataAndFallback()
    {
        $l10N = new L10N([], []);

        $this->assertSame('', $l10N->getPlural(1, 'IntegerRule'));
    }

    public function testGetStringWithExistedDataRule()
    {
        $rule = 'IntegerRule';
        $string = 'int6Type1';

        $data = [
            $rule => 'int6Type1',
        ];

        $l10N = new L10N($data);

        $this->assertSame($string, $l10N->getString($rule));
    }

    public function testGetStringWithExistedFallbackRule()
    {
        $rule = 'IntegerRule';
        $string = 'int6Type1';
        $string2 = 'int6Type1';

        $data = [
            $rule => $string,
        ];

        $fallback = [
            $rule => $string2,
        ];

        $l10N = new L10N($data, $fallback);

        $this->assertSame('', $l10N->getString('int6Type2'));
    }
}
