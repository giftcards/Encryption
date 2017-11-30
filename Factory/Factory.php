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
    protected $objectType;
    protected $registry;

    /**
     * Builder constructor.
     * @param $objectType
     * @param BuilderInterface[]|Registry $builders
     */
    public function __construct($objectType, $builders = array())
    {
        if (is_array($builders)) {
            $builders = new Registry($builders);
        }

        $this->objectType = $objectType;
        $this->registry = $builders;
    }

    public function create($name, array $options)
    {
        $factory = $this->registry->get($name);
        $resolver = new OptionsResolver();
        $factory->configureOptionsResolver($resolver);
        $object = $factory->build($resolver->resolve($options));
        
        if (!$object instanceof $this->objectType) {
            throw new WrongTypeBuiltException($this->objectType, $object);
        }
        
        return $object;
    }

    /**
     * @return array|BuilderInterface[]|Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

}
