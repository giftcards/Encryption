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
     * @param array $options
     * @return \Traversable|Data[]
     */
    public function load(array $options);
    /**
     * @param Data $encryptedData
     */
    public function save(Data $encryptedData);
}
