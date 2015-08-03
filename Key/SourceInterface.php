<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 5:58 PM
 */

namespace Omni\Encryption\Key;

interface SourceInterface
{
    public function has($key);
    public function get($key);
}