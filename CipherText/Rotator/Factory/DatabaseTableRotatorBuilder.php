<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/8/15
 * Time: 5:54 PM
 */

namespace Omni\Encryption\CipherText\Rotator\Factory;

use Omni\Encryption\CipherText\Rotator\DatabaseTableRotator;
use Omni\Encryption\Factory\BuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatabaseTableRotatorBuilder implements BuilderInterface
{
    public function build(array $options)
    {
        return new DatabaseTableRotator(
            $options['pdo'],
            $options['table'],
            $options['fields'],
            $options['id_field']
        );
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array(
                'pdo',
                'table',
                'fields',
                'id_field'
            ))
            ->setAllowedTypes(array(
                'pdo' => 'PDO',
                'table' => 'string',
                'fields' => 'array',
                'id_field' => 'string'
            ))
        ;
    }

    public function getName()
    {
        return 'database_table';
    }
}
