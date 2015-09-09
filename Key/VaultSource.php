<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/11/15
 * Time: 5:58 PM
 */

namespace Giftcards\Encryption\Key;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\ClientErrorResponseException;

class VaultSource implements SourceInterface
{
    protected $client;
    protected $mount;
    protected $valueField;
    protected $apiVersion;

    /**
     * VaultSource constructor.
     * @param ClientInterface $client
     * @param string $mount
     * @param string $valueField
     * @param string $apiVersion
     */
    public function __construct(ClientInterface $client, $mount = 'secret', $valueField = 'value', $apiVersion = 'v1')
    {
        $this->client = $client;
        $this->mount = $mount;
        $this->valueField = $valueField;
        $this->apiVersion = $apiVersion;
    }

    public function has($key)
    {
        try {
            $this->client->get(sprintf('/%s/%s/%s', $this->apiVersion, $this->mount, $key))->send();
            return true;
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() != 404) {
                throw $e;
            }
        }
        
        return false;
    }

    public function get($key)
    {
        try {
            $data = $this->client->get(sprintf('/%s/%s/%s', $this->apiVersion, $this->mount, $key))->send()->json();
            return $data['data'][$this->valueField];
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() != 404) {
                throw $e;
            }
        }

        throw new KeyNotFoundException($key);
    }
}
