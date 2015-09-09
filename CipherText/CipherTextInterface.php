<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 11:03 PM
 */
namespace Giftcards\Encryption\CipherText;

use Giftcards\Encryption\Profile\Profile;

interface CipherTextInterface
{
    /**
     * @return mixed
     */
    public function getText();

    /**
     * @return Profile
     */
    public function getProfile();
}