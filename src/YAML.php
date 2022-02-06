<?php
/**
 * YAML handler (last modified: 2022.02.06).
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
     * @var string The preferred style of quotes to use for strings.
     */
    public $Quotes = '"';

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
     * @var string Used for coercing blocks.
     */
    private $LastResolvedTag = '';

    /**
     * @var string The tag/release the version of this file belongs to (might
     *      be needed by some implementations to ensure compatibility).
     * @link https://github.com/Maikuolan/Common/tags
     */
    public const VERSION = '2.7.0';

    /**
     * Can optionally begin processing data as soon as the object is
     * instantiated, or just instantiate first, and manually make any needed
     * calls afterwards if preferred.
     *
     * @param string $In The data to process.
     * @return void
     */
    public function __construct(string $In = '')
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
    public function __toString(): string
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
    public function process(string $In, array &$Arr, int $Depth = 0, bool $Refs = false): bool
    {
        /** Guard for potentially invalid data. */
        if (strpos($In, "\n") === false) {
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
            if ($ThisTab > $Depth) {
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
                $Success = true;
                if (!$this->MultiLine && !$this->MultiLineFolded) {
                    if (!isset($Arr[$Key]) || !is_array($Arr[$Key])) {
                        $Arr[$Key] = [];
                    }
                    $Success = $this->process($SendTo, $Arr[$Key], $TabLen);
                } else {
                    $this->tryStringDataTraverseByRef($SendTo);
                    $Arr[$Key] = $SendTo;
                }
                if (isset($ThisBlockTag) && $ThisBlockTag !== '') {
                    $Arr[$Key] = $this->coerce($Arr[$Key], false, $ThisBlockTag);
                }
                if (!$Success) {
                    return false;
                }
                $SendTo = '';
            }
            if (!$this->processLine($ThisLine, $ThisTab, $Key, $Value, $Arr)) {
                return false;
            }
            $ThisBlockTag = $this->LastResolvedTag;
        }
        $Success = true;
        if ($SendTo && !empty($Key)) {
            if (!$this->MultiLine && !$this->MultiLineFolded) {
                if (!isset($Arr[$Key]) || !is_array($Arr[$Key])) {
                    $Arr[$Key] = [];
                }
                $Success = $this->process($SendTo, $Arr[$Key], $TabLen);
            } else {
                $this->tryStringDataTraverseByRef($SendTo);
                $Arr[$Key] = $SendTo;
            }
            if (isset($ThisBlockTag) && $ThisBlockTag !== '') {
                $Arr[$Key] = $this->coerce($Arr[$Key], false, $ThisBlockTag);
            }
        }
        return $Success;
    }

    /**
     * Reconstruct YAML.
     *
     * @param array $Arr The array to reconstruct from.
     * @param bool $UseCaptured Whether to use captured values.
     * @param bool $DoWithAnchors Whether to try reconstructing anchors.
     * @return string The reconstructed YAML.
     */
    public function reconstruct(array $Arr, bool $UseCaptured = false, bool $DoWithAnchors = false): string
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
    public function tryStringDataTraverseByRef(&$Data): void
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
    private function normaliseValue(string &$Value, bool $EnforceScalar = false): void
    {
        /** Resolve tags. */
        if (preg_match('~^!([!\dA-Za-z]+)(?: (.*))?$~', $Value, $Resolved)) {
            $Tag = strtolower($Resolved[1]);
            if (!$EnforceScalar) {
                $this->LastResolvedTag = $Tag;
            }
            $Value = $Resolved[2] ?? '';
            if ($Value === '|' || $Value === '') {
                return;
            }
        } else {
            $Tag = '';
        }

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

            /** Check for flow sequence. */
            if (substr($Value, 0, 1) === '[' && substr($Value, -1) === ']') {
                $Value = explode(',', substr($Value, 1, -1));
                foreach ($Value as &$ThisValue) {
                    $ThisValue = trim($ThisValue);
                    $this->normaliseValue($ThisValue);
                }
                if ($Tag !== '') {
                    $Value = $this->coerce($Value, $EnforceScalar, $Tag);
                }
                return;
            }

            /** Check for flow mapping. */
            if (substr($Value, 0, 1) === '{' && substr($Value, -1) === '}') {
                $Value = explode(',', substr($Value, 1, -1));
                $NewArr = [];
                foreach ($Value as $Entry) {
                    if (($CPos = strpos($Entry, ': ')) === false) {
                        continue;
                    }
                    $NewKey = trim(substr($Entry, 0, $CPos));
                    $this->normaliseValue($NewKey, true);
                    $NewValue = trim(substr($Entry, $CPos + 2));
                    $this->normaliseValue($NewValue);
                    $NewArr[$NewKey] = $NewValue;
                }
                $Value = $Tag !== '' ? $this->coerce($NewArr, $EnforceScalar, $Tag) : $NewArr;
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
                if ($Tag !== '') {
                    $Value = $this->coerce($Value, $EnforceScalar, $Tag);
                }
                return;
            }
        }

        /** Executed only for keys. */
        if ($EnforceScalar) {
            $Value = trim($Value);
            if ($Tag !== '') {
                $Value = $this->coerce($Value, $EnforceScalar, $Tag);
            } elseif (preg_match('~^\d+$~', $Value)) {
                $Value = (int)$Value;
            }
            return;
        }

        if ($Tag !== '') {
            $Value = $this->coerce($Value, $EnforceScalar, $Tag);
            return;
        }

        $ValueLow = strtolower($Value);
        if ($ValueLow === 'true' || $ValueLow === 'y' || $Value === '+') {
            $Value = true;
        } elseif ($ValueLow === 'false' || $ValueLow === 'n' || $Value === '-' || $ValueLen === 0) {
            $Value = false;
        } elseif ($ValueLow === 'null' || $Value === '~') {
            $Value = null;
        } elseif (preg_match('~^0x[\dA-Fa-f]+$~', $Value)) {
            $Value = hexdec(substr($Value, 2));
        } elseif (preg_match('~^0o[0-8]+$~', $Value)) {
            $Value = octdec(substr($Value, 2));
        } elseif (preg_match('~^0b[01]+$~', $Value)) {
            $Value = bindec(substr($Value, 2));
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
    private function processLine(string &$ThisLine, int &$ThisTab, &$Key, &$Value, array &$Arr): bool
    {
        /** Reset last resolved tag. */
        $this->LastResolvedTag = '';

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
        } elseif (substr($ThisLine, $ThisTab, 2) === '? ') {
            $Key = substr($ThisLine, $ThisTab + 2);
            $this->normaliseValue($Key, true);
            $Value = null;
            $Arr[$Key] = null;
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
    private function processInner(array $Arr, string &$Out, int $Depth = 0): void
    {
        $Sequential = (array_keys($Arr) === range(0, count($Arr) - 1));
        $NullSet = $this->isNullSet($Arr);
        foreach ($Arr as $Key => $Value) {
            if ($Key === '---' && $Value === false) {
                $Out .= "---\n";
                continue;
            }
            $ThisDepth = str_repeat($this->Indent, $Depth);
            if ($NullSet && !$Sequential) {
                $Out .= $ThisDepth . '?';
                $Value = $Key;
            } else {
                $Out .= $ThisDepth . ($Sequential ? '-' : $Key . ':');
            }
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
            } elseif (strpos($Value, "\n") !== false) {
                $ToAdd = "|\n" . $ThisDepth . $this->Indent;
                $ToAdd .= str_replace("\n", "\n" . $ThisDepth . $this->Indent, $this->escape($Value, false));
            } elseif (is_string($Value)) {
                $Value = $this->escape($Value);
                if ($this->FoldedAt > 0 && strpos($Value, ' ') !== false && strlen($Value) >= $this->FoldedAt) {
                    $ToAdd = ">\n" . $ThisDepth . $this->Indent . wordwrap(
                        $Value,
                        $this->FoldedAt,
                        "\n" . $ThisDepth . $this->Indent
                    );
                } else {
                    $ToAdd = $this->Quotes . $Value . $this->Quotes;
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
     * Escape according to the YAML specification.
     *
     * @param string $Value The string to escape.
     * @param bool $Newlines Whether to escape newlines.
     * @return string The escaped string.
     */
    private function escape(string $Value = '', bool $Newlines = true): string
    {
        if ($this->Quotes === "'" && $this->EscapeBySpec) {
            return str_replace("'", "''", $Value);
        }
        if ($this->Quotes !== '"') {
            return $Value;
        }
        $Value = str_replace("\\", "\\\\", $Value);
        if ($Newlines) {
            $Value = str_replace("\n", '\n', $Value);
        }
        $Value = str_replace(
            ['#', "\0", "\7", "\8", "\t", "\x0B", "\x0C", "\x0D", "\x1B", "\xC2\x85", "\xC2\xA0", "\xE2\x80\xA8", "\xE2\x80\xA9"],
            ['\#', '\0', '\a', '\b', '\t', '\v', '\f', '\r', '\e', '\N', '\_', '\L', '\P'],
            $Value
        );
        $Value = preg_replace_callback([
            '~[\x01-\x06\x0E\x0F\x10-\x1A\x1C-\x1F\x7F\xC0\xC1\xF5-\xFF]~',
            '~[\xC2-\xDF](?![\x80-\xBF])~',
            '~\xE0(?![\xA0-\xBF][\x80-\xBF])~',
            '~[\xE1-\xEC](?![\x80-\xBF]{2})~',
            '~\xED(?![\x80-\x9F][\x80-\xBF])~',
            '~\xF0(?![\x90-\xBF][\x80-\xBF]{2})~',
            '~[\xF1-\xF3](?![\x80-\xBF]{3})~',
            '~\xF4(?![\x80-\x8F][\x80-\xBF]{2})~',
            '~(?<=[\x00-\x7F\xF5-\xFF])[\x80-\xBF]~',
            '~(?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF])~',
            '~(?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2})~',
            '~(?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF])~'
        ], function ($Match) {
            return '\\x' . bin2hex($Match[0]);
        }, $Value);
        if ($this->EscapeBySpec) {
            $Value = str_replace(['"', '/'], ['\"', '\/'], $Value);
        }
        return $Value;
    }

    /**
     * Unescape according to the YAML specification.
     *
     * @param string $Value The string to unescape.
     * @param string $Style The quote style used.
     * @return string The unescaped string.
     */
    private function unescape(string $Value = '', string $Style = '"'): string
    {
        if ($Style === '"' || $Style === "\xe2\x80\x9c" || $Style === "\x91") {
            $Value = str_replace(
                ['\#', '\0', '\a', '\b', '\t', '\n', '\v', '\f', '\r', '\e', '\"', '\/', '\N', '\_', '\L', '\P', "\\\\"],
                ['#', "\0", "\x07", "\x08", "\t", "\n", "\x0B", "\x0C", "\x0D", "\x1B", '"', '/', "\xC2\x85", "\xC2\xA0", "\xE2\x80\xA8", "\xE2\x80\xA9", "\\"],
                $Value
            );
            $Captured = [];
            if (preg_match_all('~\\\\x([\dA-Fa-f]{2})~', $Value, $Captured)) {
                $Captured = array_unique($Captured[1]);
                foreach ($Captured as $Bytes) {
                    $Value = str_replace('\\x' . $Bytes, hex2bin($Bytes), $Value);
                }
            }
            $Captured = [];
            if (preg_match_all('~\\\\u([\dA-Fa-f]{4})~', $Value, $Captured)) {
                set_error_handler(function ($errno) {
                    return;
                });
                $Captured = array_unique($Captured[1]);
                foreach ($Captured as $Bytes) {
                    $Decoded = hex2bin($Bytes);
                    $Attempt = iconv('UTF-16BE', 'UTF-8', $Decoded);
                    $Reversed = $Attempt === false ? '' : iconv('UTF-8', 'UTF-16BE', $Attempt);
                    if ($Attempt !== false && strcmp($Reversed, $Decoded) === 0) {
                        $Decoded = $Attempt;
                    }
                    $Value = str_replace('\\u' . $Bytes, $Decoded, $Value);
                }
                restore_error_handler();
            }
            $Captured = [];
            if (preg_match_all('~\\\\U([\dA-Fa-f]{8})~', $Value, $Captured)) {
                set_error_handler(function ($errno) {
                    return;
                });
                $Captured = array_unique($Captured[1]);
                foreach ($Captured as $Bytes) {
                    $Decoded = hex2bin($Bytes);
                    $Attempt = iconv('UTF-32BE', 'UTF-8', $Decoded);
                    $Reversed = $Attempt === false ? '' : iconv('UTF-8', 'UTF-32BE', $Attempt);
                    if ($Attempt !== false && strcmp($Reversed, $Decoded) === 0) {
                        $Decoded = $Attempt;
                    }
                    $Value = str_replace('\\U' . $Bytes, $Decoded, $Value);
                }
                restore_error_handler();
            }
            return $Value;
        }
        if ($Style === "'" || $Style === "\xe2\x80\x98" || $Style === "\x93") {
            return str_replace("''", "'", $Value);
        }
        return $Value;
    }

    /**
     * Check whether an array is a null set.
     *
     * @param array $Arr The array.
     * @return bool True for null set; False otherwise.
     */
    private function isNullSet(array $Arr): bool
    {
        foreach ($Arr as $Value) {
            if ($Value !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Coerces a value according to the specified tag.
     *
     * @param mixed $Value The value to be coerced.
     * @param bool $EnforceScalar Whether to enforce using scalar data.
     * @param string $Tag The resolved tag.
     * @return mixed The coerced value.
     */
    private function coerce($Value, bool $EnforceScalar, string $Tag)
    {
        if ($Tag === '!null') {
            return null;
        }
        if (!$EnforceScalar) {
            if ($Tag === '!map' || $Tag === '!omap') {
                if (!is_array($Value)) {
                    if (is_string($Value)) {
                        $this->normaliseValue($Value);
                    }
                    return [$Value];
                }
                $Arr = [];
                foreach ($Value as $ThisKey => $ThisValue) {
                    if (is_string($ThisKey)) {
                        $this->normaliseValue($ThisKey, true);
                    }
                    if (is_string($ThisValue)) {
                        $this->normaliseValue($ThisValue);
                    }
                    $Arr[$ThisKey] = $ThisValue;
                }
                return $Arr;
            }
            if ($Tag === '!seq') {
                if (!is_array($Value)) {
                    if (is_string($Value)) {
                        $this->normaliseValue($Value);
                    }
                    return [$Value];
                }
                $Arr = [];
                foreach ($Value as $ThisValue) {
                    if (is_string($ThisValue)) {
                        $this->normaliseValue($ThisValue);
                    }
                    $Arr[] = $ThisValue;
                }
                return $Arr;
            }
            if ($Tag === '!set') {
                if (!is_array($Value)) {
                    return [$Value => null];
                }
                $Arr = [];
                foreach ($Value as $ThisValue) {
                    if (!is_scalar($ThisValue)) {
                        continue;
                    }
                    $Arr[$ThisValue] = null;
                }
                return $Arr;
            }
        }
        if (is_string($Value)) {
            $ValueLen = strlen($Value);
            $ValueLow = strtolower($Value);
        } else {
            if (is_array($Value)) {
                $ValueLen = count($Value);
            } else {
                $ValueLen = empty($Value) ? 0 : 1;
            }
            $ValueLow = '';
        }
        if ($Tag === '!bool') {
            if (is_bool($Value)) {
                return $Value;
            }
            if (!is_scalar($Value)) {
                return $ValueLen > 0;
            }
            if ($ValueLow === 'true' || $ValueLow === 'y' || $Value === '+') {
                return true;
            }
            if ($ValueLow === 'false' || $ValueLow === 'n' || $Value === '-' || $ValueLen === 0 || $ValueLow === 'null' || $Value === '~') {
                return false;
            }
            return (bool)$Value;
        }
        if ($Tag === '!float') {
            if (is_float($Value)) {
                return $Value;
            }
            if (!is_scalar($Value)) {
                return (float)$ValueLen;
            }
            if ($ValueLow === 'true' || $ValueLow === 'y' || $Value === '+') {
                return 1.0;
            }
            return (float)$Value;
        }
        if ($Tag === '!int') {
            if (is_int($Value)) {
                return $Value;
            }
            if (!is_scalar($Value)) {
                return $ValueLen;
            }
            if ($ValueLow === 'true' || $ValueLow === 'y' || $Value === '+') {
                return 1;
            }
            return (int)$Value;
        }
        if ($Tag === '!str') {
            if ($Value === null) {
                return 'null';
            }
            if ($Value === true) {
                return 'true';
            }
            if ($Value === false) {
                return 'false';
            }
            if (is_string($Value)) {
                return $Value;
            }
            return is_scalar($Value) ? (string)$Value : '';
        }
        if ($Tag === '!binary') {
            if ($Value === '' || !is_string($Value)) {
                return '';
            }
            return base64_decode(preg_replace('~\s~', '', $Value));
        }
        return $Value;
    }
}
