<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/1/15
 * Time: 10:11 AM
 */

namespace Giftcards\Encryption\Key\Factory;

use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Key\ContainerParametersSource;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainerParametersSourceBuilder implements BuilderInterface
{
    protected $container;

    /**
     * ContainerParameterSourceBuilder constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function build(array $options)
    {
        return new ContainerParametersSource($options['container']);
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('container'))
            ->setAllowedTypes(array('container' => 'Symfony\Component\DependencyInjection\ContainerInterface'))
        ;
        
        if ($this->container) {
            $resolver->setDefaults(array('container' => $this->container));
        }
    }

    public function getName()
    {
        return 'container_parameters';
    }
}
