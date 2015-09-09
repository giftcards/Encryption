<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 9:11 PM
 */

namespace Giftcards\Encryption\Key\Factory;

use Guzzle\Http\Client;
use Giftcards\Encryption\Factory\BuilderInterface;
use Giftcards\Encryption\Key\VaultSource;
use Giftcards\Encryption\Vault\AddTokenPlugin;
use Giftcards\Encryption\Vault\TokenAuthTokenSource;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VaultSourceBuilder implements BuilderInterface
{
    public function build(array $options)
    {
        $vaultClient = new Client($options['base_url']);
        $vaultClient->addSubscriber(
            new AddTokenPlugin(new TokenAuthTokenSource($options['token']))
        );

        return new VaultSource(
            $vaultClient,
            $options['mount'],
            $options['value_field'],
            $options['api_version']
        );
    }

    public function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('base_url', 'token'))
            ->setDefaults(array(
                'mount' => 'secret',
                'value_field' => 'value',
                'api_version' => 'v1'
            ))
            ->setAllowedTypes(array(
                'base_url' => 'string',
                'token' => 'string',
                'mount' => 'string',
                'value_field' => 'string',
                'api_version' => 'string',
            ))
        ;
    }

    public function getName()
    {
        return 'vault';
    }
}
