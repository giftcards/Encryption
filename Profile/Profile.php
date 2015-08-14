<?php
namespace Omni\Encryption\Profile;

/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 10:31 PM
 */
class Profile
{
    protected $algorithm;
    protected $keyName;

    /**
     * Profile constructor.
     * @param $algorithm
     * @param $keyName
     */
    public function __construct($algorithm, $keyName)
    {
        $this->algorithm = $algorithm;
        $this->keyName = $keyName;
    }

    /**
     * @return mixed
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }
}
