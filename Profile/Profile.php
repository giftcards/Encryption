<?php
namespace Giftcards\Encryption\Profile;

/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 10:31 PM
 */
class Profile
{
    protected $cipher;
    protected $keyName;

    /**
     * Profile constructor.
     * @param $cipher
     * @param $keyName
     */
    public function __construct($cipher, $keyName)
    {
        $this->cipher = $cipher;
        $this->keyName = $keyName;
    }

    /**
     * @return mixed
     */
    public function getCipher()
    {
        return $this->cipher;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    public function equals(self $profile)
    {
        return
            $this->getCipher() == $profile->getCipher()
            && $this->getKeyName() == $profile->getKeyName()
        ;
    }
}
