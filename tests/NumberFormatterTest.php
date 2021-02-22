<?php
/**
 * Number formatter tests file.
 *
 * This file is a part of the "common classes package", utilised by a number of
 * packages and projects, including CIDRAM and phpMussel.
 * Source: https://github.com/Maikuolan/Common
 */

require $ClassesDir . $Case . '.php';

$Expected = '
Base 2: 111,011,100,110,101,100,101,000,000,000
Base 3: 2,120,200,200,021,010,001
Base 4: 323,212,230,220,000
Base 5: 4,022,000,000,000
Base 6: 243,121,245,344
Base 7: 33,531,600,616
Base 8: 7,346,545,000
Base 9: 2,520,607,101
Base 10: 1,000,000,000
Base 11: 473,523,88a
Base 12: 23a,a93,854
Base 13: 12c,23a,19c
Base 14: 96,b4b,6b6
Base 15: 5c,bd1,46a
Base 16: 3b,9ac,a00
Base 17: 27,750,aa7
Base 18: 1b,73h,dda
Base 19: 12,4g6,g1i
Base 20: f,ca0,000
Base 21: b,dhi,eed
Base 22: 8,i0i,7fa
Base 23: 6,h8a,c3k
Base 24: 5,5e1,n2g
Base 25: 4,2a0,000
Base 26: 3,647,joc
Base 27: 2,fii,731
Base 28: 2,22p,q5k
Base 29: 1,jlp,2ii
Base 30: 1,b4h,13a
Base 31: 1,3sp,5mg
Base 32: tpl,ig0
Base 33: pi7,fla
Base 34: m0a,nuo
Base 35: j1d,lik
Base 36: gjd,gxs';

$Actual = '';

$Obj = new \Maikuolan\Common\NumberFormatter();

for ($Obj->Base = 2; $Obj->Base < 37; $Obj->Base++) {
    $Actual .= "\nBase " . $Obj->Base . ': ' . $Obj->format(1e+9);
}

if ($Actual !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Expected = '
Base 2: 1,010.1000 ~ 100,000,000.0100
Base 3: 101.1111 ~ 100,111.0202
Base 4: 22.2000 ~ 10,000.1000
Base 5: 20.2222 ~ 2,011.1111
Base 6: 14.3000 ~ 1,104.1300
Base 7: 13.3333 ~ 514.1515
Base 8: 12.4000 ~ 400.2000
Base 9: 11.4444 ~ 314.2222
Base 10: 10.5000 ~ 256.2500
Base 11: a.5555 ~ 213.2828
Base 12: a.6000 ~ 194.3000
Base 13: a.6666 ~ 169.3333
Base 14: a.7000 ~ 144.3700
Base 15: a.7777 ~ 121.3b3b
Base 16: a.8000 ~ 100.4000
Base 17: a.8888 ~ f1.4444
Base 18: a.9000 ~ e4.4900
Base 19: a.9999 ~ d9.4e4e
Base 20: a.a000 ~ cg.5000
Base 21: a.aaaa ~ c4.5555
Base 22: a.b000 ~ be.5b00
Base 23: a.bbbb ~ b3.5h5h
Base 24: a.c000 ~ ag.6000
Base 25: a.cccc ~ a6.6666
Base 26: a.d000 ~ 9m.6d00
Base 27: a.dddd ~ 9d.6k6k
Base 28: a.e000 ~ 94.7000
Base 29: a.eeee ~ 8o.7777
Base 30: a.f000 ~ 8g.7f00
Base 31: a.ffff ~ 88.7n7n
Base 32: a.g000 ~ 80.8000
Base 33: a.gggg ~ 7p.8888
Base 34: a.h000 ~ 7i.8h00
Base 35: a.hhhh ~ 7b.8q8q
Base 36: a.i000 ~ 74.9000
';

$Obj = new \Maikuolan\Common\NumberFormatter();

$Actual = '';
for ($Obj->Base = 2; $Obj->Base < 37; $Obj->Base++) {
    $Actual .= "\nBase " . $Obj->Base . ': ' . $Obj->format('10.5', 4) . ' ~ ' . $Obj->format('256.25', 4);
}
$Actual .= "\n";

$ExitCode++;
if ($Actual !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}

$Formats = [
    'Arabic-1',
    'Arabic-2',
    'Arabic-3',
    'Arabic-4',
    'Armenian',
    'Base-12',
    'Base-16',
    'Bengali-1',
    'Burmese-1',
    'China-1',
    'Chinese-Simplified',
    'Chinese-Simplified-Financial',
    'Chinese-Traditional',
    'Chinese-Traditional-Financial',
    'Fullwidth',
    'Hebrew',
    'India-1',
    'India-2',
    'India-3',
    'India-4',
    'India-5',
    'India-6',
    'Japanese',
    'Javanese',
    'Khmer-1',
    'Lao-1',
    'Latin-1',
    'Latin-2',
    'Latin-3',
    'Latin-4',
    'Latin-5',
    'Mayan',
    'Mongolian',
    'NoSep-1',
    'NoSep-2',
    'Odia',
    'Roman',
    'SDN-Dwiggins',
    'SDN-Pitman',
    'Tamil',
    'Thai-1',
    'Thai-2',
    'Tibetan'
];

$Expected = '
`Arabic-1` | `١٢٣٤٥٦٧٫٨٩` | `١٠٢٠٣٠٤٠٫٥٠٦٠٧` | `١٠٠٫٧٥٠`
`Arabic-2` | `١٬٢٣٤٬٥٦٧٫٨٩` | `١٠٬٢٠٣٬٠٤٠٫٥٠٦٠٧` | `١٠٠٫٧٥٠`
`Arabic-3` | `۱٬۲۳۴٬۵۶۷٫۸۹` | `۱۰٬۲۰۳٬۰۴۰٫۵۰۶۰۷` | `۱۰۰٫۷۵۰`
`Arabic-4` | `۱۲٬۳۴٬۵۶۷٫۸۹` | `۱٬۰۲٬۰۳٬۰۴۰٫۵۰۶۰۷` | `۱۰۰٫۷۵۰`
`Armenian` | `Ռ̅Մ̅Լ̅ՏՇԿԷ` | `Մ̅ՎԽ` | `Ճ`
`Base-12` | `4b6547.a8` | `3500654.60a5a` | `84.900`
`Base-16` | `12d687.e3` | `9bafa0.818dd` | `64.c00`
`Bengali-1` | `১২,৩৪,৫৬৭.৮৯` | `১,০২,০৩,০৪০.৫০৬০৭` | `১০০.৭৫০`
`Burmese-1` | `၁၂၃၄၅၆၇.၈၉` | `၁၀၂၀၃၀၄၀.၅၀၆၀၇` | `၁၀၀.၇၅၀`
`China-1` | `123,4567.89` | `1020,3040.50607` | `100.750`
`Chinese-Simplified` | `一百二十三万四千五百六十七点八九` | `一千二十三千四十点五〇六〇七` | `一百点七五〇`
`Chinese-Simplified-Financial` | `壹佰贰拾叁萬肆仟伍佰陆拾柒点捌玖` | `壹仟贰拾叁仟肆拾点伍零陆零柒` | `壹佰点柒伍零`
`Chinese-Traditional` | `一百二十三萬四千五百六十七點八九` | `一千二十三千四十點五零六零七` | `一百點七五零`
`Chinese-Traditional-Financial` | `壹佰貳拾叄萬肆仟伍佰陸拾柒點捌玖` | `壹仟貳拾叄仟肆拾點伍零陸零柒` | `壹佰點柒伍零`
`Fullwidth` | `１２３４５６７.８９` | `１０２０３０４０.５０６０７` | `１００.７５０`
`Hebrew` | `א׳׳ב׳קג׳יד׳ךסז` | `א׳י׳ב׳קג׳מ` | `ק`
`India-1` | `12,34,567.89` | `1,02,03,040.50607` | `100.750`
`India-2` | `१२,३४,५६७.८९` | `१,०२,०३,०४०.५०६०७` | `१००.७५०`
`India-3` | `૧૨,૩૪,૫૬૭.૮૯` | `૧,૦૨,૦૩,૦૪૦.૫૦૬૦૭` | `૧૦૦.૭૫૦`
`India-4` | `੧੨,੩੪,੫੬੭.੮੯` | `੧,੦੨,੦੩,੦੪੦.੫੦੬੦੭` | `੧੦੦.੭੫੦`
`India-5` | `೧೨,೩೪,೫೬೭.೮೯` | `೧,೦೨,೦೩,೦೪೦.೫೦೬೦೭` | `೧೦೦.೭೫೦`
`India-6` | `౧౨,౩౪,౫౬౭.౮౯` | `౧,౦౨,౦౩,౦౪౦.౫౦౬౦౭` | `౧౦౦.౭౫౦`
`Japanese` | `百万二十万三万四千五百六十七・八九分` | `千万二十万三千四十・五六厘七糸` | `百・七五分`
`Javanese` | `꧑꧒꧓꧔꧕꧖꧗.꧘꧙` | `꧑꧐꧒꧐꧓꧐꧔꧐.꧕꧐꧖꧐꧗` | `꧑꧐꧐.꧗꧕꧐`
`Khmer-1` | `១.២៣៤.៥៦៧,៨៩` | `១០.២០៣.០៤០,៥០៦០៧` | `១០០,៧៥០`
`Lao-1` | `໑໒໓໔໕໖໗.໘໙` | `໑໐໒໐໓໐໔໐.໕໐໖໐໗` | `໑໐໐.໗໕໐`
`Latin-1` | `1,234,567.89` | `10,203,040.50607` | `100.750`
`Latin-2` | `1 234 567.89` | `10 203 040.50607` | `100.750`
`Latin-3` | `1.234.567,89` | `10.203.040,50607` | `100,750`
`Latin-4` | `1 234 567,89` | `10 203 040,50607` | `100,750`
`Latin-5` | `1,234,567·89` | `10,203,040·50607` | `100·750`
`Mayan` | `𝋧𝋮𝋦𝋨𝋧.𝋱𝋰` | `𝋣𝋣𝋯𝋧𝋬𝋠.𝋪𝋢𝋨𝋫𝋦` | `𝋥𝋠.𝋯𝋠𝋠`
`Mongolian` | `᠑᠒᠓᠔᠕᠖᠗.᠘᠙` | `᠑᠐᠒᠐᠓᠐᠔᠐.᠕᠐᠖᠐᠗` | `᠑᠐᠐.᠗᠕᠐`
`NoSep-1` | `1234567.89` | `10203040.50607` | `100.750`
`NoSep-2` | `1234567,89` | `10203040,50607` | `100,750`
`Odia` | `୧୨୩୪୫୬୭.୮୯` | `୧୦୨୦୩୦୪୦.୫୦୬୦୭` | `୧୦୦.୭୫୦`
`Roman` | `M̅C̅C̅X̅X̅X̅I̅V̅DLXVII` | `C̅C̅MMMXL` | `C`
`SDN-Dwiggins` | `4E6,547;X8` | `3,500,654;60X5X` | `84;900`
`SDN-Pitman` | `4↋6,547;↊8` | `3,500,654;60↊5↊` | `84;900`
`Tamil` | `௲௲௨௱௲௩௰௲௪௲௫௱௬௰௭` | `௰௲௲௨௱௲௩௲௪௰` | `௱`
`Thai-1` | `๑,๒๓๔,๕๖๗.๘๙` | `๑๐,๒๐๓,๐๔๐.๕๐๖๐๗` | `๑๐๐.๗๕๐`
`Thai-2` | `๑๒๓๔๕๖๗.๘๙` | `๑๐๒๐๓๐๔๐.๕๐๖๐๗` | `๑๐๐.๗๕๐`
`Tibetan` | `༡༢༣༤༥༦༧.༨༩` | `༡༠༢༠༣༠༤༠.༥༠༦༠༧` | `༡༠༠.༧༥༠`
';

$Actual = '';
foreach ($Formats as $Format) {
    $Obj = new \Maikuolan\Common\NumberFormatter($Format);
    $Actual .= "\n`" . $Format . '` | `' . $Obj->format('1234567.89', 2) . '` | `' . $Obj->format('10203040.50607080', 5) . '` | `' . $Obj->format('100.75', 3) . '`';
}
$Actual .= "\n";

$ExitCode++;
if ($Actual !== $Expected) {
    echo 'Test failed: ' . $Case . ':L' . __LINE__ . '().' . PHP_EOL;
    exit($ExitCode);
}
