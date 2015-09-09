<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 6:40 PM
 */

namespace Omni\Encryption\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Factory
{
    protected $registry;
    protected $objectType;
    protected $objects = array();

    public static function newInstance($factories = null)
    {
        return new static($factories);
    }

    /**
     * Builder constructor.
     * @param null|array|Registry $factories
     */
    public function __construct($factories = null)
    {
        if (is_array($factories)) {
            $factories = new Registry($factories);
        }

        $this->registry = $factories;
    }

    public function create($name, array $options)
    {
        $factory = $this->registry->get($name);
        $resolver = new OptionsResolver();
        $factory->configureOptionsResolver($resolver);
        return $factory->build($resolver->resolve($options));
    }

    public function addBuilder(BuilderInterface $builder)
    {
        $this->registry->add($builder);
        return $this;
    }
}
