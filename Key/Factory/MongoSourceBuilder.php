<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/1/15
 * Time: 9:46 AM
 */

namespace Omni\Encryption\Key\Factory;

use Doctrine\MongoDB\Connection;
use Omni\Encryption\Factory\BuilderInterface;
use Omni\Encryption\Key\MongoSource;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
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
            ->setAllowedTypes(array(
                'connection' => array('Doctrine\MongoDB\Connection', 'array'),
                'database' => 'string',
                'collection' => 'string',
                'find_by_field' => 'string',
                'value_field' => 'string'
            ))
            ->setNormalizer('connection', function ($_, $connection) {
                if (!is_array($connection)) {
                    return $connection;
                }
                
                $resolver = new OptionsResolver();
                $resolver
                    ->setDefaults(array(
                        'server' => null,
                        'options' => array(),
                        'configuration' => null,
                        'event_manager' => null
                    ))
                    ->setAllowedTypes(array(
                        'server' => array('string', 'MongoClient', 'Mongo'),
                        'options' => 'array',
                        'configuration' => 'Doctrine\MongoDB\Configuration',
                        'event_manager' => 'Doctrine\Common\EventManager'
                    ))
                ;
                
                try {
                    $connection = $resolver->resolve($connection);
                } catch (InvalidArgumentException $e) {
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
            })
        ;
    }

    public function getName()
    {
        return 'mongo';
    }
}
