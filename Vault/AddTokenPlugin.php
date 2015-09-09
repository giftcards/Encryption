<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 6:03 PM
 */

namespace Giftcards\Encryption\Vault;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddTokenPlugin implements EventSubscriberInterface
{
    protected $tokenSource;
    
    public static function getSubscribedEvents()
    {
        return array('client.create_request' => array('addAuthToken', 255));
    }

    /**
     * AddTokenPlugin constructor.
     * @param $tokenSource
     */
    public function __construct(AuthTokenSourceInterface $tokenSource)
    {
        $this->tokenSource = $tokenSource;
    }

    public function addAuthToken(Event $event)
    {
        $event['request']->addHeader(
            'X-Vault-Token',
            $this->tokenSource->getToken($event['request'])
        );
    }
}
