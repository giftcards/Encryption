<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/14/17
 * Time: 3:54 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistry;
use Giftcards\Encryption\Encryptor;
use Giftcards\Encryption\Factory\BuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RotatorBuilder implements BuilderInterface
{
    /**
     * @param array $options
     * @return Rotator
     */
    public function build(array $options)
    {
        return new Rotator($options['encryptor'], $options['storeRegistry']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array(
                'encryptor',
                'storeRegistry',
            ))
            ->setAllowedTypes('encryptor', Encryptor::class)
            ->setAllowedTypes('storeRegistry', StoreRegistry::class)
        ;
    }

    public function getName()
    {
        return "rotator";
    }
}