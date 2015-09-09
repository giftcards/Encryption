<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 3:05 PM
 */

namespace Giftcards\Encryption\Cipher;

class CipherRegistryBuilder
{
    /** @var  CipherInterface[] */
    protected $ciphers = array();

    public static function newInstance()
    {
        $builder = new static();
        return $builder
            ->add(new MysqlAes())
            ->add(new NoOp())
        ;
    }

    /**
     * @return CipherRegistry
     */
    public function build()
    {
        $cipherRegistry = new CipherRegistry();

        foreach ($this->ciphers as $name => $cipher) {
            $cipherRegistry->add($cipher);
        }

        return $cipherRegistry;
    }

    /**
     * @param CipherInterface $cipher
     * @return $this
     */
    public function add(CipherInterface $cipher)
    {
        $this->ciphers[] = $cipher;
        return $this;
    }
}
