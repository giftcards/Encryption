<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 9:36 PM
 */

namespace Omni\Encryption\CipherText;

use Omni\Encryption\Profile\Profile;

class CipherText implements CipherTextInterface
{
    protected $text;
    protected $profile;

    /**
     * CipherText constructor.
     * @param $text
     * @param Profile $profile
     */
    public function __construct($text, Profile $profile)
    {
        $this->text = $text;
        $this->profile = $profile;
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
        return $this->profile;
    }
}
