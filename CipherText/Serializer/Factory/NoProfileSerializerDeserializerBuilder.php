<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/10/15
 * Time: 12:46 PM
 */

namespace Giftcards\Encryption\CipherText\Serializer\Factory;

use Giftcards\Encryption\CipherText\Serializer\BasicSerializerDeserializer;
use Giftcards\Encryption\CipherText\Serializer\NoProfileSerializerDeserializer;
use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Profile\Profile;
use Giftcards\Encryption\Profile\ProfileRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoProfileSerializerDeserializerBuilder implements BuilderInterface
{
    protected $profileRegistry;

    /**
     * NoProfileSerializerDeserializerBuilder constructor.
     * @param $profileRegistry
     */
    public function __construct(ProfileRegistry $profileRegistry = null)
    {
        $this->profileRegistry = $profileRegistry;
    }

    public function build(array $options)
    {
        return new NoProfileSerializerDeserializer($options['profile']);
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $allowedProfileTypes = array('Giftcards\Encryption\Profile\Profile');
        $resolver->setRequired(array('profile'));
        
        if ($this->profileRegistry) {
            $allowedProfileTypes[] = 'string';
            $profileRegistry = $this->profileRegistry;
            
            $resolver->setNormalizers(array('profile' => function ($_, $profile) use ($profileRegistry) {
                if ($profile instanceof Profile) {
                    return $profile;
                }
                
                return $profileRegistry->get((string)$profile);
            }));
        }
        
        $resolver->setAllowedTypes(array('profile' => $allowedProfileTypes));
    }

    public function getName()
    {
        return 'no_profile';
    }
}
