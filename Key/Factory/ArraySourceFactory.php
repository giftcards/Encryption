<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/1/15
 * Time: 10:11 AM
 */

namespace Omni\Encryption\Key\Factory;

use Omni\Encryption\Key\ArraySource;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArraySourceFactory implements SourceFactoryInterface
{
    public function build(array $options)
    {
        return new ArraySource($options['keys']);
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('keys'))
            ->setAllowedTypes(array('keys' => 'array'))
        ;
    }

    public function getName()
    {
        return 'array';
    }
}
