<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 11:03 PM
 */
namespace Omni\Encryption\CipherText;

use Omni\Encryption\Profile\Profile;

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