<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/1/15
 * Time: 10:11 AM
 */

namespace Giftcards\Encryption\Key\Factory;

use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Key\ArraySource;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArraySourceBuilder implements BuilderInterface
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
