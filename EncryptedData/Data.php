<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:20 PM
 */

namespace Omni\Encryption\EncryptedData;

class Data
{
    protected $id;
    protected $data;
    protected $keyName;

    /**
     * EncryptedData constructor.
     * @param $id
     * @param $data
     */
    public function __construct($id, array $data, $keyName)
    {
        $this->id = $id;
        $this->data = $data;
        $this->keyName = $keyName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }
}
