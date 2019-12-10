<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 7:34 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Giftcards\Encryption\Key\IniFileSource;

class IniFileSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $source1 = new IniFileSource(__DIR__.'/../Fixtures/keys.ini');
        $source2 = new IniFileSource(__DIR__.'/../Fixtures/keys.ini', true);
        return [
            [
                $source1,
                ['key1' => 'hello1', 'KEY2' => 'hello2', 'key3' => 'goodbye1', 'key4' => 'goodbye2'],
                ['key5']
            ],
            [
                $source2,
                ['key1' => 'hello1', 'key2' => 'hello2', 'KEY3' => 'goodbye1', 'KEY4' => 'goodbye2'],
                ['key3', 'key4', 'key5']
            ],
        ];
    }
}
