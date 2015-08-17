<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 10:33 PM
 */

namespace Omni\Encryption\Profile;

class ProfileRegistry
{
    /** @var Profile[] */
    protected $profiles = array();

    public function set($name, Profile $profile)
    {
        $this->profiles[$name] = $profile;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->profiles[$name]);
    }

    /**
     * @param $name
     * @return Profile
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new ProfileNotFoundException($name);
        }
        
        return $this->profiles[$name];
    }

    /**
     * @return Profile[]
     */
    public function all()
    {
        return $this->profiles;
    }
}
