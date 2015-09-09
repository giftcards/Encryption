<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/2/15
 * Time: 6:18 PM
 */

namespace Giftcards\Encryption\Key;

class NoneSource extends AbstractSource
{
    public function has($key)
    {
        return $key == 'none';
    }

    protected function getKey($key)
    {
        return '';
    }
}
