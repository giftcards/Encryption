<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 12/8/16
 * Time: 1:26 PM
 */

namespace Giftcards\Encryption\Doctrine;

class FieldData
{
    protected $clearText;
    protected $cipherText;
    protected $profile;

    public function __construct($clearText, $cipherText, $profile)
    {
        $this->clearText = $clearText;
        $this->cipherText = $cipherText;
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getClearText()
    {
        return $this->clearText;
    }

    /**
     * @return mixed
     */
    public function getCipherText()
    {
        return $this->cipherText;
    }

    /**
     * @return null|string
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
