<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:15 PM
 */

namespace Omni\Encryption\EncryptedData;

interface StoreInterface
{
    /**
     * @return string
     */
    public function getName();
    /**
     * @param array $options
     * @return \Traversable|EncryptedData[]
     */
    public function load(array $options);
    /**
     * @param EncryptedData $encryptedData
     */
    public function save(EncryptedData $encryptedData);
}
