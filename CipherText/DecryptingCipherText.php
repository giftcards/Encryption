<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 9:41 PM
 */

namespace Omni\Encryption\CipherText;

use Omni\Encryption\Encryptor;
use Omni\Encryption\Profile\Profile;

class DecryptingCipherText implements CipherTextInterface
{
    protected $innerText;
    protected $encryptor;

    /**
     * DecryptingCipherText constructor.
     * @param $innerText
     * @param $encryptor
     */
    public function __construct(CipherTextInterface $innerText, Encryptor $encryptor)
    {
        $this->innerText = $innerText;
        $this->encryptor = $encryptor;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->innerText->getText();
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->innerText->getProfile();
    }

    public function getClearText()
    {
        return $this->encryptor->decrypt($this->innerText);
    }

    public function __toString()
    {
        return (string)$this->getText();
    }
}
