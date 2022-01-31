<?php
/**
 * YAML handler (last modified: 2022.01.31).
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 *
 * "COMMON CLASSES PACKAGE" COPYRIGHT 2019 and beyond by Caleb Mazalevskis.
 * *This particular class*, COPYRIGHT 2016 and beyond by Caleb Mazalevskis.
 *
 * Note: Some parts of the YAML specification aren't supported by this class.
 * See the included documentation for more information.
 */

namespace Maikuolan\Common;

class YAML
{
    /**
     * @var array An array to contain all the data processed by the handler.
     */
    public $Data = [];

    /**
     * @var array Used as a data source for inline variables.
     */
    public $Refs = [];

    /**
     * @var string Default indent to use when reconstructing YAML data.
     */
    public $Indent = ' ';

    /**
     * @var string Last indent used when processing YAML data.
     */
    public $LastIndent = '';

    /**
     * @var string Captured header comments from the YAML data.
     */
    public $CapturedHeader = '';

    /**
     * @var int Single line to folded multi-line string length limit.
     */
    public $FoldedAt = 120;

    /**
     * @var array Used to cache any anchors found in the document.
     * @link https://yaml.org/spec/1.2.2/#692-node-anchors
     */
    public $Anchors = [];

    /**
     * @var bool Whether to escape according to the YAML specification.
     * @link https://yaml.org/spec/1.2.2/#57-escaped-characters
     */
    public $EscapeBySpec = false;

    /**
     * @var bool Whether to render multi-line values.
     */
    private $MultiLine = false;

    /**
     * @var bool Whether to render folded multi-line values.
     */
    private $MultiLineFolded = false;

    /**
     * @var array Used to determine which anchors have been reconstructed.
     */
    private $AnchorsDone = [];

    /**
     * @var bool Whether to try reconstructing anchors during reconstruction.
     */
    private $DoWithAnchors = false;

    /**
     * @var string Encoding used by the most recent process input.
     */
    private $LastInputEncoding = '';

    /**
     * @var \Maikuolan\Common\Demojibakefier Used to support various encodings.
     */
    private $Demojibakefier = null;

    /**
     * @var string The tag/release the version of this file belongs to (might
     *      be needed by some implementations to ensure compatibility).
     * @link https://github.com/Maikuolan/Common/tags
     */
    const VERSION = '1.7.0';

    /**
     * Can optionally begin processing data as soon as the object is
     * instantiated, or just instantiate first, and manually make any needed
     * calls afterwards if preferred.
     *
     * @param string $In The data to process.
     * @return void
     */
    public function __construct($In = '')
    {
        if ($In) {
            $this->process($In, $this->Data, 0, true);
        }
    }

    /**
     * PHP's magic "__toString" method to act as an alias for "reconstruct".
     *
     * @return string
     */
    public function __toString()
    {
        return $this->reconstruct($this->Data);
    }

    /**
     * Process YAML data.
     *
     * @param string $In The data to be processed.
     * @param array $Arr Where to store the processed data.
     * @param int $Depth Tab depth (inherited through recursion; ignore it).
     * @param bool $Refs Whether to set refs for inline variables.
     * @return bool True when entire process completes successfully. False to exit early.
     */
    public function process($In, array &$Arr, $Depth = 0, $Refs = false)
    {
        /** Guard for potentially invalid data. */
        if (!is_string($In) || strpos($In, "\n") === false) {
            return false;
        }

        /** Assign refs array for inline variables. */
        if ($Refs) {
            $this->Refs = &$Arr;
        }

        /** Things to do at the beginning of the process execution. */
        if ($Depth === 0) {
            $this->MultiLine = false;
            $this->MultiLineFolded = false;
            $this->LastIndent = '';
            $this->CapturedHeader = '';
            $Captured = [];

            /** Support various encodings. */
            if (class_exists('\Maikuolan\Common\Demojibakefier')) {
                $this->Demojibakefier = new \Maikuolan\Common\Demojibakefier();

                /**
                 * Attempt to determine input encoding.
                 * @link https://yaml.org/spec/1.2.2/#52-character-encodings
                 */
                if (preg_match('~^\0\0(?:\0|\xFE\xFF)~', $In)) {
                    $In = substr($In, 4);
                    $this->LastInputEncoding = 'UTF-32BE';
                } elseif (preg_match('~^(?:\xFF\xFE|.\0)\0\0~', $In)) {
                    $In = substr($In, 4);
                    $this->LastInputEncoding = 'UTF-32LE';
                } elseif (preg_match('~^(?:\xFE\xFF|\0)~', $In)) {
                    $In = substr($In, 2);
                    $this->LastInputEncoding = 'UTF-16BE';
                } elseif (preg_match('~^(?:\xFF\xFE|.\0)~', $In)) {
                    $In = substr($In, 2);
                    $this->LastInputEncoding = 'UTF-16LE';
                } else {
                    if (substr($In, 0, 3) === "\xEF\xBB\xBF") {
                        $In = substr($In, 3);
                    }
                    $this->LastInputEncoding = 'UTF-8';
                }

                /** Fail if non-compliant. */
                if (!$this->Demojibakefier->checkConformity($In, $this->LastInputEncoding)) {
                    return false;
                }

                /** Attempt to normalise encoding if not already UTF-8. */
                if ($this->LastInputEncoding !== 'UTF-8') {
                    /** Suppress errors to avoid potentially flooding logs. */
                    set_error_handler(function ($errno) {
                        return;
                    });

                    $Attempt = iconv($this->LastInputEncoding, 'UTF-8', $In);
                    if (
                        $Attempt === false ||
                        !$this->Demojibakefier->checkConformity($Attempt, 'UTF-8') ||
                        strcmp(iconv('UTF-8', $this->LastInputEncoding, $Attempt), $In) !== 0
                    ) {
                        return false;
                    }
                    $In = $Attempt;

                    /** We're done.. Restore the error handler. */
                    restore_error_handler();
                }
            }

            /** Attempt to capture header comments. */
            if (preg_match('~^(##\\\\(?:\n#[^\n]*)+\n##/\n\n|(?:#[^\n]*\n)+\n)~m', $In, $Captured)) {
                $this->CapturedHeader = $Captured[0];
            }
        }

        $In = str_replace("\r", '', $In);
        $Key = '';
        $Value = '';
        $SendTo = '';
        $TabLen = 0;
        $SoL = 0;
        while ($SoL !== false) {
            $ThisLine = (
                ($EoL = strpos($In, "\n", $SoL)) === false
            ) ? substr($In, $SoL) : substr($In, $SoL, $EoL - $SoL);
            $SoL = ($EoL === false) ? false : $EoL + 1;
            if (!($ThisLine = preg_replace(['/(?<!\\\)#.*$/', '/\s+$/'], '', $ThisLine))) {
                continue;
            }
            $ThisTab = 0;
            while (($Chr = substr($ThisLine, $ThisTab, 1)) && ($Chr === ' ' || $Chr === "\t")) {
                $ThisTab++;
            }
            if ($this->LastIndent === '') {
                $this->LastIndent = str_repeat(substr($ThisLine, 0, 1), $ThisTab);
            }
            if (($ThisTab > $Depth)) {
                if ($TabLen === 0) {
                    $TabLen = $ThisTab;
                }
                if (!$this->MultiLine && !$this->MultiLineFolded) {
                    $SendTo .= $ThisLine . "\n";
                } else {
                    if ($SendTo) {
                        if ($this->MultiLine) {
                            $SendTo .= "\n";
                        } elseif (substr($ThisLine, $TabLen, 1) !== ' ' && substr($SendTo, -1) !== ' ') {
                            $SendTo .= ' ';
                        }
                    }
                    $SendTo .= substr($ThisLine, $TabLen);
                }
                continue;
            } elseif ($ThisTab < $Depth) {
                return false;
            } elseif ($SendTo) {
                if (empty($Key)) {
                    return false;
                }
                if (!$this->MultiLine && !$this->MultiLineFolded) {
                    if (!isset($Arr[$Key]) || !is_array($Arr[$Key])) {
                        $Arr[$Key] = [];
                    }
                    if (!$this->process($SendTo, $Arr[$Key], $TabLen)) {
                        return false;
                    }
                } else {
                    $this->tryStringDataTraverseByRef($SendTo);
                    $Arr[$Key] = $SendTo;
                }
                $SendTo = '';
            }
            if (!$this->processLine($ThisLine, $ThisTab, $Key, $Value, $Arr)) {
                return false;
            }
        }
        if ($SendTo && !empty($Key)) {
            if (!$this->MultiLine && !$this->MultiLineFolded) {
                if (!isset($Arr[$Key]) || !is_array($Arr[$Key])) {
                    $Arr[$Key] = [];
                }
                if (!$this->process($SendTo, $Arr[$Key], $TabLen)) {
                    return false;
                }
            } else {
                $this->tryStringDataTraverseByRef($SendTo);
                $Arr[$Key] = $SendTo;
            }
        }
        return true;
    }

    /**
     * Reconstruct YAML.
     *
     * @param array $Arr The array to reconstruct from.
     * @param bool $UseCaptured Whether to use captured values.
     * @param bool $DoWithAnchors Whether to try reconstructing anchors.
     * @return string The reconstructed YAML.
     */
    public function reconstruct(array $Arr, $UseCaptured = false, $DoWithAnchors = false)
    {
        $Out = '';
        $this->DoWithAnchors = (count($this->Anchors) && $DoWithAnchors);
        if ($UseCaptured) {
            if ($this->LastIndent !== '') {
                $this->Indent = $this->LastIndent;
            }
            if ($this->CapturedHeader !== '') {
                $Out .= $this->CapturedHeader;
            }
        }
        $this->processInner($Arr, $Out);
        $this->AnchorsDone = [];
        $this->DoWithAnchors = false;
        return $Out;
    }

    /**
     * Traverse data path.
     *
     * @param mixed $Data The data to traverse.
     * @param string|array $Path The path to traverse.
     * @return mixed The traversed data, or an empty string on failure.
     */
    public function dataTraverse(&$Data, $Path = [])
    {
        if (!is_array($Path)) {
            $Path = preg_split('~(?<!\\\)\.~', $Path) ?: [];
        }
        $Segment = array_shift($Path);
        if ($Segment === null || strlen($Segment) === 0) {
            return is_scalar($Data) ? $Data : '';
        }
        $Segment = str_replace('\.', '.', $Segment);
        if (is_array($Data)) {
            return isset($Data[$Segment]) ? $this->dataTraverse($Data[$Segment], $Path) : '';
        }
        return $this->dataTraverse($Data, $Path);
    }

    /**
     * Attempt string data path traverse by reference.
     *
     * @param mixed $Data The data to traverse.
     * @return void
     */
    public function tryStringDataTraverseByRef(&$Data)
    {
        if (
            empty($this->Refs) ||
            !is_string($Data) ||
            !preg_match_all('~\{\{ ?([^\r\n{}]+) ?\}\}~', $Data, $VarMatches) ||
            !isset($VarMatches[0][0], $VarMatches[1][0])
        ) {
            return;
        }
        $MatchCount = count($VarMatches[0]);
        for ($Index = 0; $Index < $MatchCount; $Index++) {
            if (($Extracted = $this->dataTraverse($this->Refs, $VarMatches[1][$Index])) && is_string($Extracted)) {
                $Data = str_replace($VarMatches[0][$Index], $Extracted, $Data);
            }
        }
    }

    /**
     * Normalises the values defined by the processLine method.
     *
     * @param string $Value The value to be normalised.
     * @param bool $EnforceScalar Whether to enforce using scalar data.
     * @return void
     */
    private function normaliseValue(&$Value, $EnforceScalar = false)
    {
        /** Not executed for keys. */
        if (!$EnforceScalar) {
            /** Check for anchors and populate if necessary. */
            $AnchorMatches = [];
            if (
                preg_match('~^&([\dA-Za-z]+) +(.*)$~', $Value, $AnchorMatches) &&
                isset($AnchorMatches[1], $AnchorMatches[2])
            ) {
                $Value = $AnchorMatches[2];
                $this->Anchors[$AnchorMatches[1]] = $Value;
            } elseif (
                preg_match('~^\*([\dA-Za-z]+)$~', $Value, $AnchorMatches) &&
                isset($AnchorMatches[1], $this->Anchors[$AnchorMatches[1]])
            ) {
                $Value = $this->Anchors[$AnchorMatches[1]];
            }

            /** Check for inline variables. */
            $this->tryStringDataTraverseByRef($Value);

            /** Check for inline arrays. */
            if (substr($Value, 0, 1) === '[' && substr($Value, -1) === ']') {
                $Value = explode(',', substr($Value, 1, -1));
                foreach ($Value as &$ThisValue) {
                    $ThisValue = trim($ThisValue);
                    $this->normaliseValue($ThisValue);
                }
                return;
            }
        }

        if (!$this->EscapeBySpec) {
            $Value = str_replace(['\#', "\\\\"], ['#', "\\"], $Value);
        }
        $ValueLen = strlen($Value);

        /** Check for string quotes. */
        foreach ([
            ['"', '"', 1],
            ["'", "'", 1],
            ['`', '`', 1],
            ["\x91", "\x92", 1],
            ["\x93", "\x94", 1],
            ["\xe2\x80\x98", "\xe2\x80\x99", 3],
            ["\xe2\x80\x9c", "\xe2\x80\x9d", 3]
        ] as $Wrapper) {
            if (substr($Value, 0, $Wrapper[2]) === $Wrapper[0] && substr($Value, $ValueLen - $Wrapper[2]) === $Wrapper[1]) {
                $Value = substr($Value, $Wrapper[2], $ValueLen - ($Wrapper[2] * 2));
                if ($this->EscapeBySpec) {
                    $Value = $this->unescape($Value, $Wrapper[0]);
                }
                return;
            }
        }

        /** Executed only for keys. */
        if ($EnforceScalar) {
            if (preg_match('~^\d+$~', $Value)) {
                $Value = (int)$Value;
            }
            return;
        }

        $ValueLow = strtolower($Value);
        if ($ValueLow === 'true' || $ValueLow === 'y' || $Value === '+') {
            $Value = true;
        } elseif ($ValueLow === 'false' || $ValueLow === 'n' || $Value === '-' || $ValueLen === 0) {
            $Value = false;
        } elseif ($ValueLow === 'null' || $Value === '~') {
            $Value = null;
        } elseif (substr($Value, 0, 2) === '0x' && ($HexTest = substr($Value, 2)) && !preg_match('/[^\da-f]/i', $HexTest) && !($ValueLen % 2)) {
            $Value = hex2bin($HexTest);
        } elseif (preg_match('~^\d+$~', $Value)) {
            $Value = (int)$Value;
        } elseif (preg_match('~^(?:\d+\.\d+|\d+(?:\.\d+)?[Ee][-+]\d+)$~', $Value)) {
            $Value = (float)$Value;
        }
    }

    /**
     * Process a single line of YAML input.
     *
     * @param string $ThisLine The line to be processed.
     * @param int $ThisTab The size of the line indentation.
     * @param string|int $Key Line key.
     * @param string|int|bool $Value Line value.
     * @param array $Arr Where to store the data.
     * @return bool True when entire process completes successfully. False to exit early.
     */
    private function processLine(&$ThisLine, &$ThisTab, &$Key, &$Value, array &$Arr)
    {
        if ($ThisLine === '---') {
            $Key = '---';
            $Value = false;
            $Arr[$Key] = $Value;
        } elseif (substr($ThisLine, -1) === ':' && strpos($ThisLine, ': ') === false) {
            $Key = substr($ThisLine, $ThisTab, -1);
            $this->normaliseValue($Key, true);
            if (!isset($Arr[$Key])) {
                $Arr[$Key] = false;
            }
            $Value = false;
        } elseif (substr($ThisLine, $ThisTab, 2) === '- ') {
            $Value = substr($ThisLine, $ThisTab + 2);
            $ValueLen = strlen($Value);
            $this->normaliseValue($Value);
            if ($ValueLen > 0) {
                $Arr[] = $Value;
            }
        } elseif (($DelPos = strpos($ThisLine, ': ')) !== false) {
            $Key = substr($ThisLine, $ThisTab, $DelPos - $ThisTab);
            $KeyLen = strlen($Key);
            $this->normaliseValue($Key, true);
            if (!$Key) {
                if (substr($ThisLine, $ThisTab, $DelPos - $ThisTab + 2) !== '0: ') {
                    return false;
                }
                $Key = 0;
            }
            $Value = substr($ThisLine, $ThisTab + $KeyLen + 2);
            $ValueLen = strlen($Value);
            $this->normaliseValue($Value);
            if ($ValueLen > 0) {
                $Arr[$Key] = $Value;
            }
        } elseif (substr($ThisLine, -1) === '-') {
            $Arr[] = false;
            end($Arr);
            $Key = key($Arr);
            reset($Arr);
            $Value = false;
        } elseif (strpos($ThisLine, ':') === false && strlen($ThisLine) > 1) {
            $Key = $ThisLine;
            $this->normaliseValue($Key, true);
            if (!isset($Arr[$Key])) {
                $Arr[$Key] = false;
            }
            $Value = false;
        }
        $this->MultiLine = ($Value === '|');
        $this->MultiLineFolded = ($Value === '>');
        return true;
    }

    /**
     * Reconstruct an inner level of YAML (shouldn't be called directly).
     *
     * @param array $Arr The array to reconstruct from.
     * @param string $Out The reconstructed YAML.
     * @param int $Depth The level depth.
     * @return void
     */
    private function processInner(array $Arr, &$Out, $Depth = 0)
    {
        $Sequential = (array_keys($Arr) === range(0, count($Arr) - 1));
        foreach ($Arr as $Key => $Value) {
            if ($Key === '---' && $Value === false) {
                $Out .= "---\n";
                continue;
            }
            $ThisDepth = str_repeat($this->Indent, $Depth);
            $Out .= $ThisDepth . ($Sequential ? '-' : $Key . ':');
            if (is_array($Value)) {
                $Out .= "\n";
                $this->processInner($Value, $Out, $Depth + 1);
                continue;
            }
            $Out .= ' ';
            if ($Value === true) {
                $ToAdd = 'true';
            } elseif ($Value === false) {
                $ToAdd = 'false';
            } elseif ($Value === null) {
                $ToAdd = 'null';
            } elseif (preg_match(
                '~[^\t\n\r\x20-\xff]|' .
                '[\xc2-\xdf](?![\x80-\xbf])|' .
                '\xe0(?![\xa0-\xbf][\x80-\xbf])|' .
                '[\xe1-\xec](?![\x80-\xbf]{2})|' .
                '\xed(?![\x80-\x9f][\x80-\xbf])|' .
                '\xf0(?![\x90-\xbf][\x80-\xbf]{2})[\xf0-\xf3](?![\x80-\xbf]{3})\xf4(?![\x80-\x9f][\x80-\xbf]{2})~',
                $Value
            )) {
                $ToAdd = '0x' . strtolower(bin2hex($Value));
            } elseif (strpos($Value, "\n") !== false) {
                $ToAdd = "|\n" . $ThisDepth . $this->Indent;
                if ($this->EscapeBySpec) {
                    $ToAdd .= str_replace("\n", "\n" . $ThisDepth . $this->Indent, $this->escape($Value, false));
                } else {
                    $ToAdd .= str_replace(["\n", "\\", '#'], ["\n" . $ThisDepth . $this->Indent, "\\\\", '\#'], $Value);
                }
            } elseif (is_string($Value)) {
                if ($this->EscapeBySpec) {
                    $Value = $this->escape($Value);
                } else {
                    $Value = str_replace(["\\", '#'], ["\\\\", '\#'], $Value);
                }
                if ($this->FoldedAt > 0 && strpos($Value, ' ') !== false && strlen($Value) >= $this->FoldedAt) {
                    $ToAdd = ">\n" . $ThisDepth . $this->Indent . wordwrap(
                        $Value,
                        $this->FoldedAt,
                        "\n" . $ThisDepth . $this->Indent
                    );
                } else {
                    $ToAdd = '"' . $Value . '"';
                }
            } else {
                $ToAdd = $Value;
            }
            if ($this->DoWithAnchors) {
                foreach ($this->Anchors as $Name => $Data) {
                    if ($Data === $ToAdd) {
                        if (empty($this->AnchorsDone[$Name])) {
                            $ToAdd = '&' . $Name . ' ' . $ToAdd;
                            $this->AnchorsDone[$Name] = true;
                        } else {
                            $ToAdd = '*' . $Name;
                        }
                        break;
                    }
                }
            }
            $Out .= $ToAdd . "\n";
        }
    }

    /**
     * Escape according to the YAML specification (this implementation prefers
     * double-quotes over single-quotes for reconstruction).
     *
     * @param string $Value The string to escape.
     * @param bool $Newlines Whether to escape newlines.
     * @return string The escaped string.
     */
    private function escape($Value = '', $Newlines = true)
    {
        $Value = str_replace("\\", "\\\\", $Value);
        if ($Newlines) {
            $Value = str_replace("\n", '\n', $Value);
        }
        return str_replace(
            ['#', "\0", "\x07", "\x08", "\t", "\x0B", "\x0C", "\x0D", "\x1B", '"', '/', "\xC2\x85", "\xC2\xA0", "\xE2\x80\xA8", "\xE2\x80\xA9"],
            ['\#', '\0', '\a', '\b', '\t', '\v', '\f', '\r', '\e', '\"', '\/', '\N', '\_', '\L', '\P'],
            $Value
        );
    }

    /**
     * Unescape according to the YAML specification.
     *
     * @param string $Value The string to unescape.
     * @param string $Style The quote style used.
     * @return string The unescaped string.
     */
    private function unescape($Value = '', $Style = '"')
    {
        if ($Style === '"' || $Style === "\xe2\x80\x9c" || $Style === "\x91") {
            return str_replace(
                ['\#', '\0', '\a', '\b', '\t', '\n', '\v', '\f', '\r', '\e', '\"', '\/', '\N', '\_', '\L', '\P', "\\\\"],
                ['#', "\0", "\x07", "\x08", "\t", "\n", "\x0B", "\x0C", "\x0D", "\x1B", '"', '/', "\xC2\x85", "\xC2\xA0", "\xE2\x80\xA8", "\xE2\x80\xA9", "\\"],
                $Value
            );
        }
        if ($Style === "'" || $Style === "\xe2\x80\x98" || $Style === "\x93") {
            return str_replace("''", "'", $Value);
        }
        return $Value;
    }
}
