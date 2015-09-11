<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 5:49 PM
 */

namespace Giftcards\Encryption\Factory;

class Registry
{
    /** @var BuilderInterface[] */
    protected $builders = array();

    public function __construct(array $builders = array())
    {
        foreach ($builders as $builder) {
            $this->add($builder);
        }
    }

    public function add(BuilderInterface $builder)
    {
        $this->builders[$builder->getName()] = $builder;
        return $this;
    }

    public function has($name)
    {
        return isset($this->builders[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new BuilderNotFoundException($name);
        }
        
        return $this->builders[$name];
    }

    public function all()
    {
        return $this->builders;
    }
}
