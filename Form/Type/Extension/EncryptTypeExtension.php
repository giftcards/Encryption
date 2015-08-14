<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/13/15
 * Time: 9:50 PM
 */

namespace Omni\Encryption\Form\Type\Extension;


use Omni\Encryption\Encryptor;
use Omni\Encryption\Form\DataTransformer\EncryptTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EncryptTypeExtension extends AbstractTypeExtension
{
    protected $cipherTextGenerator;

    /**
     * EncryptTypeExtension constructor.
     * @param Encryptor $cipherTextGenerator
     */
    public function __construct(Encryptor $cipherTextGenerator)
    {
        $this->cipherTextGenerator = $cipherTextGenerator;
    }

    /**
     * Builds the form.
     *
     * This method is called after the extended type has built the form to
     * further modify it.
     *
     * @see FormTypeInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['encryption_profile']) {
            return;
        }
        
        $builder->addModelTransformer(new EncryptTransformer(
            $this->cipherTextGenerator,
            $options['encryption_profile']
        ));
    }

    /**
     * Overrides the default options from the extended type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     *
     * @deprecated since version 2.7, to be removed in 3.0.
     *             Use the method configureOptions instead. This method will be
     *             added to the FormTypeExtensionInterface with Symfony 3.0
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'encrypt' => false
            ))
            ->setAllowedTypes(array('encryption_profile' => array('false', 'string')))
        ;
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
