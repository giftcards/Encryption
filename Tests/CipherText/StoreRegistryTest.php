<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/17/15
 * Time: 6:13 PM
 */

namespace Omni\Encryption\Tests\CipherText;

use Omni\Encryption\CipherText\StoreRegistry;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class StoreRegistryTest extends AbstractExtendableTestCase
{
    protected $registry;

    public function setUp()
    {
        $this->registry = new StoreRegistry();
    }
}
