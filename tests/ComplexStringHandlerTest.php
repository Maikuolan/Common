<?php

namespace Maikuolan\Common\Tests;

use PHPUnit\Framework\TestCase;
use Maikuolan\Common\ComplexStringHandler;

class ComplexStringHandlerTest extends TestCase
{
    public function testConstructor()
    {
        $TheString = 'ab0cd1ef2gh3ij4kl5mn6op7qr8st9uv';
        $ThePattern = '~(\D+)~';

        $ComplexStringHandler = new ComplexStringHandler($TheString, $ThePattern, function ($Data) {
            return ($Data === '' || $Data === false) ? '' : ' "' . (((int)$Data + 1)) . '" ';
        });

        $this->assertInstanceOf(ComplexStringHandler::class, $ComplexStringHandler);
    }

    public function testIterateClosure()
    {
        $TheString = 'ab0cd1ef2gh3ij4kl5mn6op7qr8st9uv';
        $ThePattern = '~(\D+)~';

        $ComplexStringHandler = new ComplexStringHandler($TheString, $ThePattern, function ($Data) {
            return ($Data === '' || $Data === false) ? '' : ' "' . (((int)$Data + 1)) . '" ';
        });
        $ComplexStringHandler->iterateClosure(function ($Data) {
            return '(' . $Data . ')';
        }, true);

        $this->assertSame('(ab) "1" (cd) "2" (ef) "3" (gh) "4" (ij) "5" (kl) "6" (mn) "7" (op) "8" (qr) "9" (st) "10" (uv)', $ComplexStringHandler->recompile());
    }

    public function testIterateClosureOnEmptyString()
    {
        $TheString = '';
        $ThePattern = '~(\D+)~';

        $ComplexStringHandler = new ComplexStringHandler($TheString, $ThePattern, function ($Data) {
            return ($Data === '' || $Data === false) ? '' : ' "' . (((int)$Data + 1)) . '" ';
        });
        $result = $ComplexStringHandler->iterateClosure(function ($Data) {
            return '(' . $Data . ')';
        }, true);

        $this->assertNull($result);
    }

    public function testGenerateMarkers()
    {
        $TheString = 'ab0cd1ef2gh3ij4kl5mn6op7qr8st9uv';
        $ThePattern = '~(\D+)~';

        $ComplexStringHandler = new \Maikuolan\Common\ComplexStringHandler();
        $ComplexStringHandler->Input = $TheString;
        $ComplexStringHandler->generateMarkers($ThePattern);
        $ComplexStringHandler->iterateClosure(function ($Data) {
            return ($Data === '' || $Data === false) ? '' : ' "' . (((int)$Data + 1)) . '" ';
        }, false);
        $ComplexStringHandler->iterateClosure(function ($Data) {
            return '(' . $Data . ')';
        }, true);

        $this->assertSame('(ab) "1" (cd) "2" (ef) "3" (gh) "4" (ij) "5" (kl) "6" (mn) "7" (op) "8" (qr) "9" (st) "10" (uv)', $ComplexStringHandler->recompile());
    }
}
