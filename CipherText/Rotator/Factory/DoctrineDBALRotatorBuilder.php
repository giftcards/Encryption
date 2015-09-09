<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 5:54 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Factory;

use Giftcards\Encryption\CipherText\Rotator\DoctrineDBALRotator;
use Giftcards\Encryption\Factory\BuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctrineDBALRotatorBuilder implements BuilderInterface
{
    public function build(array $options)
    {
        return new DoctrineDBALRotator(
            $options['connection'],
            $options['table'],
            $options['fields'],
            $options['id_field']
        );
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array(
                'connection',
                'table',
                'fields',
                'id_field'
            ))
            ->setAllowedTypes(array(
                'connection' => 'Doctrine\DBAL\Connection',
                'table' => 'string',
                'fields' => 'array',
                'id_field' => 'string'
            ))
        ;
    }

    public function getName()
    {
        return 'doctrine_dbal';
    }
}
