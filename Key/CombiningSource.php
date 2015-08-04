<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:05 PM
 */

namespace Omni\Encryption\Key;

class CombiningSource extends AbstractSource
{
    protected $internalSource;
    protected $leftsAndRights;

    /**
     * CombiningSource constructor.
     * @param SourceInterface $internalSource
     * @param array $leftsAndRights
     */
    public function __construct(SourceInterface $internalSource, array $leftsAndRights)
    {
        $this->internalSource = $internalSource;
        $this->leftsAndRights = $leftsAndRights;
    }

    public function has($key)
    {
        return
            isset($this->leftsAndRights[$key])
            && $this->internalSource->has($this->leftsAndRights[$key]['left'])
            && $this->internalSource->has($this->leftsAndRights[$key]['right'])
        ;
    }

    public function getKey($key)
    {
        return
            $this->internalSource->get($this->leftsAndRights[$key]['left'])
            .$this->internalSource->get($this->leftsAndRights[$key]['right'])
        ;
    }
}