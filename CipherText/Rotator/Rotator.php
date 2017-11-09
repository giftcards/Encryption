<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:11 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Giftcards\Encryption\Encryptor;

class Rotator
{
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var StoreRegistry
     */
    private $storeRegistry;

    public function __construct(Encryptor $encryptor, StoreRegistry $storeRegistry)
    {
        $this->encryptor = $encryptor;
        $this->storeRegistry = $storeRegistry;
    }

    public function rotate($storeName, $newProfile, Bounds $bounds, ObserverInterface $observer)
    {
        $store = $this->storeRegistry->getStoreByName($storeName);
        /** @var int $offset */
        /** @var int $limit */
        foreach ($bounds as $key => list($offset, $limit)) {
            $records = $store->fetch($offset, $limit);
            if (!count($records)) {
                break;
            }
            $store->save(array_map(function (Record $record) use ($newProfile, $observer) {
                return $this->rotateRecord($record, $newProfile, $observer);
            }, $records));
        }
    }

    private function rotateRecord(Record $record, $newProfile, ObserverInterface $observer): Record
    {
        $observer->rotating($record->getId());
        $newData = [];
        foreach ($record->getData() as $key => $datum) {
            $newData[$key] = $this->encryptor->encrypt($this->encryptor->decrypt($datum), $newProfile);
        }
        $observer->rotated($record->getId());
        return new Record($record->getId(), $newData);
    }
}