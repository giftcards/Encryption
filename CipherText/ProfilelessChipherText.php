<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 6/5/16
 * Time: 2:39 PM
 */

namespace Giftcards\Encryption\CipherText;

use Giftcards\Encryption\Profile\NoProfileException;
use Giftcards\Encryption\Profile\Profile;

class ProfilelessChipherText implements CipherTextInterface
{
    protected $text;

    /**
     * ProfilelessChipherText constructor.
     * @param $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        throw new NoProfileException(
            'This cipher text has no profile info.
             to decrypt you must pass the profile name the to the encryptor.'
        );
    }
}
