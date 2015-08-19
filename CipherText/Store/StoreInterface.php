<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:15 PM
 */

namespace Omni\Encryption\CipherText\Store;

use Omni\Encryption\CipherText\Group;

interface StoreInterface
{
    /**
     * @param array $options
     * @return \Traversable|Group[]
     */
    public function load(array $options);
    /**
     * @param Group $encryptedData
     */
    public function save(Group $encryptedData);
}
