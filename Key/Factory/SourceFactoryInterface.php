<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 9:05 PM
 */

namespace Omni\Encryption\Key\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface SourceFactoryInterface
{
    public function build(array $options);
    public function configureOptionsResolver(OptionsResolver $resolver);
    public function getName();
}
