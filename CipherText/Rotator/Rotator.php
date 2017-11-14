<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:11 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Store\StoreRegistry;
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

    public function rotate($storeName, $newProfile, Bounds $bounds, RotatorObserverInterface $observer)
    {
        $store = $this->storeRegistry->get($storeName);
        /** @var int $offset */
        /** @var int $limit */
        foreach ($bounds as $key => list($offset, $limit)) {
            $records = $store->fetch($offset, $limit);
            $observer->fetchedRecords($offset, $limit, $records);
            if (!count($records)) {
                break;
            }
            $store->save(array_map(function (Record $record) use ($newProfile, $observer) {
                return $this->rotateRecord($record, $newProfile, $observer);
            }, $records));
            $observer->savedRecords($offset, $limit, $records);
        }
    }

    private function rotateRecord(Record $record, $newProfile, RotatorObserverInterface $observer): Record
    {
        $observer->rotatingRecord($record);
        $newData = [];
        foreach ($record->getData() as $key => $datum) {
            $newData[$key] = $this->encryptor->encrypt($this->encryptor->decrypt($datum), $newProfile);
        }
        $observer->rotatedRecord($record);
        return new Record($record->getId(), $newData);
    }
}