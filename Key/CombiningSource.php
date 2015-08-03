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
    protected $left;
    protected $right;

    /**
     * CombiningSource constructor.
     * @param $left
     * @param $right
     */
    public function __construct(SourceInterface $left, SourceInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function has($key)
    {
        return $this->left->has($key) && $this->right->has($key);
    }

    public function getKey($key)
    {
        return $this->left->get($key).$this->right->get($key);
    }
}