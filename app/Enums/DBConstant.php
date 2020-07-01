<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Constant enum.
 */
class Constant extends BaseEnum
{
    // Records limit from DB at once in batch
    const BATCH_LIMIT_ROW = 1000;

    const DAY_OF_WEEK_JP = ['日', '月', '火', '水', '木', '金', '土'];

    const DEFAULT_LIMIT_RESPONSE = 10;

    const SORT_ASC = 'ASC';

    const SORT_DESC = 'DESC';

    const DEFAULT_CURRENCY = 'JPY';

    const CHECK_GROUP = [
        //あ　→　means starting with あ or い or う or え or お
        'ア' => 'あ',
        'あ' => 'あ',
        'い' => 'あ',
        'う' => 'あ',
        'え' => 'あ',
        'お' => 'あ',
        //か　→　means starting with か or き or く or け or こ or が or ぎ or ぐ or げ or ご
        'カ' => 'か',
        'か' => 'か',
        'き' => 'か',
        'く' => 'か',
        'け' => 'か',
        'こ' => 'か',
        'が' => 'か',
        'ぎ' => 'か',
        'ぐ' => 'か',
        'ぐ' => 'か',
        'ご' => 'か',
        //さ　→　means starting with さ or し or す or せ or そ or ざ or じ or ず or ぜ or ぞ
        'サ' => 'さ',
        'さ' => 'さ',
        'し' => 'さ',
        'す' => 'さ',
        'せ' => 'さ',
        'そ' => 'さ',
        'ざ' => 'さ',
        'じ' => 'さ',
        'ず' => 'さ',
        'ぜ' => 'さ',
        'ぞ' => 'さ',
        //た　→　means starting with た or ち or つ or て or と or だ or ぢ or づ or で or ど 
        'タ' => 'た',
        'た' => 'た',
        'ち' => 'た',
        'つ' => 'た',
        'て' => 'た',
        'と' => 'た',
        'だ' => 'た',
        'ぢ' => 'た',
        'づ' => 'た',
        'で' => 'た',
        'ど' => 'た',
        //な　→　means starting with な or に or ぬ or ね or の
        'ナ' => 'な',
        'な' => 'な',
        'に' => 'な',
        'ぬ' => 'な',
        'ね' => 'な',
        'の' => 'な',
        //は　→　means starting with は or ひ or ふ or へ or ほ or ば or び or ぶ or べ or ぼ 
        'ハ' => 'は',
        'は' => 'は',
        'ひ' => 'は',
        'ふ' => 'は',
        'へ' => 'は',
        'ほ' => 'は',
        'ば' => 'は',
        'び' => 'は',
        'ぶ' => 'は',
        'べ' => 'は',
        'ぼ' => 'は',
        //ま　→　means starting with ま or み or む or め or も
        'マ' => 'ま',
        'ま' => 'ま',
        'み' => 'ま',
        'む' => 'ま',
        'め' => 'ま',
        'も' => 'ま',
        //や　→　means starting with や or ゆ or よ
        'や' => 'や',
        'ゆ' => 'や',
        'よ' => 'や',
        //ら　→　means starting with ら or り or る or れ or ろ
        'ラ' => 'ら',
        'ら' => 'ら',
        'り' => 'ら',
        'る' => 'ら',
        'れ' => 'ら',
        'ろ' => 'ら',
        //わ　→　means starting with わ or ゐ or ゑ or を or ん
        'ワ' => 'わ',
        'わ' => 'わ',
        'ゐ' => 'わ',
        'ゑ' => 'わ',
        'を' => 'わ',
        'ん' => 'わ',
        //Latinh
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
        'E' => 'E',
        'F' => 'F',
        'G' => 'G',
        'H' => 'H',
        'I' => 'I',
        'J' => 'J',
        'K' => 'K',
        'L' => 'L',
        'M' => 'M',
        'N' => 'N',
        'O' => 'O',
        'P' => 'P',
        'Q' => 'Q',
        'R' => 'R',
        'S' => 'S',
        'T' => 'T',
        'U' => 'U',
        'V' => 'V',
        'W' => 'W',
        'X' => 'X',
        'Y' => 'Y',
        'Z' => 'Z'
    ];
    
    CONST GROUP_NAME_SORT = ['あ', 'か', 'さ','た','な','は','ま','や','ら','わ',
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z', '他'];
}
