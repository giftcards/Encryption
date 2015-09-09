<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 6:40 PM
 */

namespace Giftcards\Encryption\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Factory
{
    protected $registry;
    protected $objects = array();

    /**
     * Builder constructor.
     * @param BuilderInterface[]|Registry $builders
     */
    public function __construct($builders = array())
    {
        if (is_array($builders)) {
            $builders = new Registry($builders);
        }

        $this->registry = $builders;
    }

    public function create($name, array $options)
    {
        $factory = $this->registry->get($name);
        $resolver = new OptionsResolver();
        $factory->configureOptionsResolver($resolver);
        return $factory->build($resolver->resolve($options));
    }

    /**
     * @return array|BuilderInterface[]|Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }
}
