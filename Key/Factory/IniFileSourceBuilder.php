<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/1/15
 * Time: 9:56 AM
 */

namespace Omni\Encryption\Key\Factory;

use Omni\Encryption\Factory\BuilderInterface;
use Omni\Encryption\Key\IniFileSource;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IniFileSourceBuilder implements BuilderInterface
{
    public function build(array $options)
    {
        return new IniFileSource($options['file'], $options['case_sensitive']);
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('file'))
            ->setDefaults(array('case_sensitive' => false))
            ->setAllowedTypes(array(
                'file' => 'string',
                'case_sensitive' => 'bool'
            ))
        ;
    }

    public function getName()
    {
        return 'ini';
    }
}
