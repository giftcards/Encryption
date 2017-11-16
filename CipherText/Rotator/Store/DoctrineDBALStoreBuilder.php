<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/15/17
 * Time: 7:59 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator\Store;

use Doctrine\DBAL\Connection;
use Giftcards\Encryption\Factory\BuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctrineDBALStoreBuilder implements BuilderInterface
{

    /**
     * @param array $options
     * @return DoctrineDBALStore
     */
    public function build(array $options): DoctrineDBALStore
    {
        return new DoctrineDBALStore($options['connection'], $options['table'], $options['fields'], $options['idField']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array(
                'connection',
                'table',
                'fields',
                'id_field'
            ))
            ->setAllowedTypes('connection', Connection::class)
            ->setAllowedTypes('table', 'string')
            ->setAllowedTypes('fields', 'array')
            ->setAllowedTypes('id_field', 'string')
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine_dbal';
    }

}
