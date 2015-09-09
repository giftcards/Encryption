<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/9/15
 * Time: 5:21 PM
 */

namespace Omni\Encryption\Profile;

class ProfileRegistryBuilder
{
    /** @var  Profile[] */
    protected $profiles = array();

    public static function newInstance()
    {
        return new static();
    }

    public function build()
    {
        $registry = new ProfileRegistry();

        foreach ($this->profiles as $name => $profile) {
            $registry->set($name, $profile);
        }

        return $registry;
    }

    public function set($name, $profileOrCipher, $key = null)
    {
        if (!$profileOrCipher instanceof Profile) {
            $profileOrCipher = new Profile($profileOrCipher, $key);
        }

        $this->profiles[$name] = $profileOrCipher;
        return $this;
    }
}
