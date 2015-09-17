<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/17/15
 * Time: 12:54 PM
 */

namespace Giftcards\Encryption\Tests\Key;

use Giftcards\Encryption\Key\SourceInterface;

class MockCircularSource implements SourceInterface
{
    /** @var  SourceInterface */
    protected $inner;

    /**
     * @param mixed $inner
     */
    public function setInner(SourceInterface $inner)
    {
        $this->inner = $inner;
    }
    
    public function has($key)
    {
        return $this->inner->has($key);
    }

    public function get($key)
    {
        return $this->inner->get($key);
    }
}
