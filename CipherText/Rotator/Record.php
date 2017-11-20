<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 10:26 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class Record
{
    /**
     * @var mixed
     */
    private $id;
    /**
     * @var array
     */
    private $data;

    /**
     * Record constructor.
     * @param mixed $id Unique id for this record
     * @param array $data Array of data to be rotated
     */
    public function __construct($id, array $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

}