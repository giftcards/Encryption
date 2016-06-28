<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/15
 * Time: 12:46 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer\Factory;

use Giftcards\Encryption\CipherText\Serializer\BasicSerializerDeserializer;
use Giftcards\Encryption\Factory\BuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasicSerializerDeserializerBuilder implements BuilderInterface
{
    public function build(array $options)
    {
        return new BasicSerializerDeserializer($options['separator']);
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array('separator' => ':'))
            ->setAllowedTypes('separator', 'string')
        ;
    }

    public function getName()
    {
        return 'basic';
    }
}
