<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/5/15
 * Time: 7:34 PM
 */

namespace Omni\Encryption\Tests\Key;

use Omni\Encryption\Key\IniFileSource;

class IniFileSourceTest extends AbstractSourceTest
{
    public function gettersHassersProvider()
    {
        $source1 = new IniFileSource(__DIR__.'/../Fixtures/keys.ini');
        $source2 = new IniFileSource(__DIR__.'/../Fixtures/keys.ini', true);
        return array(
            array(
                $source1,
                array('key1' => 'hello1', 'KEY2' => 'hello2', 'key3' => 'goodbye1', 'key4' => 'goodbye2'),
                array('key5')
            ),
            array(
                $source2,
                array('key1' => 'hello1', 'key2' => 'hello2', 'KEY3' => 'goodbye1', 'KEY4' => 'goodbye2'),
                array('key3', 'key4', 'key5')
            ),
        );
    }
}
