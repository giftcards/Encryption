<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 9:41 PM
 */

namespace Omni\Encryption\CipherText;

use Omni\Encryption\Encryptor;

class DecryptingCipherText implements CipherTextInterface
{
    protected $innerText;
    protected $generator;

    /**
     * DecryptingCipherText constructor.
     * @param $innerText
     * @param $generator
     */
    public function __construct(CipherTextInterface $innerText, Encryptor $generator)
    {
        $this->innerText = $innerText;
        $this->generator = $generator;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->innerText->getText();
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->innerText->getProfile();
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->innerText->getKeyName();
    }

    public function getClearText()
    {
        return $this->generator->decrypt($this->innerText);
    }

    public function __toString()
    {
        return (string)$this->getClearText();
    }
}
