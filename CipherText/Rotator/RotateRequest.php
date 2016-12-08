<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 8/18/16
 * Time: 7:43 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class RotateRequest
{
    protected $oldProfile;
    protected $newProfile;
    protected $offset;
    protected $limit;

    /**
     * RotateRequest constructor.
     * @param $oldProfile
     * @param $newProfile
     * @param $offset
     * @param $limit
     */
    public function __construct(
        $oldProfile = null,
        $newProfile = null,
        $offset = 0,
        $limit = null
    ) {
        $this->oldProfile = $oldProfile;
        $this->newProfile = $newProfile;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @return null
     */
    public function getOldProfile()
    {
        return $this->oldProfile;
    }

    /**
     * @return null
     */
    public function getNewProfile()
    {
        return $this->newProfile;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return null
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
