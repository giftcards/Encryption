<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/1/15
 * Time: 9:46 AM
 */

namespace Giftcards\Encryption\Key\Factory;

use Doctrine\MongoDB\Connection;
use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Key\MongoSource;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MongoSourceBuilder implements BuilderInterface
{
    public function build(array $options)
    {
        return new MongoSource(
            $options['connection'],
            $options['database'],
            $options['collection'],
            $options['find_by_field'],
            $options['value_field']
        );
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('connection', 'database', 'collection'))
            ->setDefaults(array(
                'find_by_field' => 'name',
                'value_field' => 'value'
            ))
            ->setAllowedTypes('connection', array('Doctrine\MongoDB\Connection', 'array'))
            ->setAllowedTypes('database', 'string')
            ->setAllowedTypes('collection', 'string')
            ->setAllowedTypes('find_by_field', 'string')
            ->setAllowedTypes('value_field', 'string')
            ->setNormalizers(array('connection' => function ($_, $connection) {
                if (!is_array($connection)) {
                    return $connection;
                }
                
                $resolver = new OptionsResolver();
                $resolver
                    ->setRequired(array('server'))
                    ->setDefaults(array(
                        'options' => array(),
                        'configuration' => null,
                        'event_manager' => null
                    ))
                    ->setAllowedTypes('server', array('string', 'MongoClient', 'Mongo'))
                    ->setAllowedTypes('options', 'array')
                    ->setAllowedTypes('configuration', 'Doctrine\MongoDB\Configuration')
                    ->setAllowedTypes('event_manager', 'Doctrine\Common\EventManager')
                ;
                
                try {
                    $connection = $resolver->resolve($connection);
                } catch (ExceptionInterface $e) {
                    throw new InvalidOptionsException(sprintf(
                        'The option "connection" does not have valid values: %s',
                        $e->getMessage()
                    ), 0, $e);
                }
                
                return new Connection(
                    $connection['server'],
                    $connection['options'],
                    $connection['configuration'],
                    $connection['event_manager']
                );
            }))
        ;
    }

    public function getName()
    {
        return 'mongo';
    }
}
