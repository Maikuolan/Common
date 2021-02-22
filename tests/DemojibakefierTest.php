<?php
/**
 * Demojibakefier tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

/** Korean placeholder text. Source: <http://yoondongju.yonsei.ac.kr/poet/poem.asp?ID=1>. */
$TextKO = '죽는 날까지 하늘을 우러러 한점 부끄럼이 없기를, 잎새에 이는 바람에도 나는 괴로워했다. 별을 노래하는 마음으로 모든 죽어가는 것을 사랑해야지 그리고 나한테 주어진 길을 걸어가야겠다. 오늘밤에도 별이 바람에 스치운다.';

/** Preamble of the United Nations Charter in Chinese <https://www.un.org/zh/sections/un-charter/preamble/index.html>. */
$TextZH = '序言我联合国人民同兹决心欲免后世再遭今代人类两度身历惨不堪言之战祸，重申基本人权，人格尊严与价值，以及男女与大小各国平等权利之信念，创造适当环境，俾克维持正义，尊重由条约与国际法其他渊源而起之义务，久而弗懈，促成大自由中之社会进步及较善之民生，并为达此目的力行容恕，彼此以善邻之道，和睦相处，集中力量，以维持国际和平及安全，接受原则，确立方法，以保证非为公共利益，不得使用武力，运用国际机构，以促成全球人民经济及社会之进展，用是发愤立志，务当同心协力，以竟厥功爰由我各本国政府，经齐集金山市之代表各将所奉全权证书，互相校阅，均属妥善，议定本联合国宪章，并设立国际组织，定名联合国。';

$DataKO = iconv('UTF-8', 'UTF-16BE', $TextKO);
$DataZH = iconv('UTF-8', 'GB18030', $TextZH);

/** Firstly, confirm it's naturally reversible (without Demojibakefier's help). */
if (iconv('UTF-16BE', 'UTF-8', $DataKO) !== $TextKO) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
$ExitCode++;
if (iconv('GB18030', 'UTF-8', $DataZH) !== $TextZH) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

/** Now we'll build our compound "wrong" data. */
$Sample = $DataKO . "\n" . $DataZH;

/** Instantiate the object. */
$Demojibakefier = new \Maikuolan\Common\Demojibakefier();

$ExitCode++;
$Reversed = explode("\n", $Demojibakefier->guard($Sample), 2);

/** Now let's check whether Demojibakefier can reverse it itself. */
$ExitCode++;
if ($Reversed[0] !== $TextKO || $Reversed[1] !== $TextZH) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

/**
 * There are still a bunch of things that don't quite entirely work as intended
 * with the Demojibakefier class, but I can't test for those things until I fix
 * them properly anyway, hence why the tests here are quite small right now.
 * I'll update the tests later, after those oddities have been weeded out.
 */
