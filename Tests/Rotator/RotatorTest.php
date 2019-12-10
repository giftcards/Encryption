<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 9:37 AM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\Bounds;
use Giftcards\Encryption\CipherText\Rotator\NullRotatorObserver;
use Giftcards\Encryption\CipherText\Rotator\Record;
use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class RotatorTest extends AbstractExtendableTestCase
{
    public function testRotation()
    {
        $records = [
            new Record(0, ['record0_encrypted']),
            new Record(1, ['record1_encrypted']),
            new Record(2, ['record2_encrypted']),
            new Record(3, ['record3_encrypted']),
        ];
        $rotatedRecords = [
            new Record(0, ['record0_rotated']),
            new Record(1, ['record1_rotated']),
            new Record(2, ['record2_rotated']),
            new Record(3, ['record3_rotated']),
        ];

        $observer = new NullRotatorObserver();

        $encryptor = Mockery::mock("Giftcards\\Encryption\\Encryptor");
        $encryptor->shouldReceive("decrypt")->with("record0_encrypted")->andReturn("record0_decrypted");
        $encryptor->shouldReceive("decrypt")->with("record1_encrypted")->andReturn("record1_decrypted");
        $encryptor->shouldReceive("decrypt")->with("record2_encrypted")->andReturn("record2_decrypted");
        $encryptor->shouldReceive("decrypt")->with("record3_encrypted")->andReturn("record3_decrypted");

        $encryptor->shouldReceive("encrypt")->with("record0_decrypted", "test_profile")->andReturn("record0_rotated");
        $encryptor->shouldReceive("encrypt")->with("record1_decrypted", "test_profile")->andReturn("record1_rotated");
        $encryptor->shouldReceive("encrypt")->with("record2_decrypted", "test_profile")->andReturn("record2_rotated");
        $encryptor->shouldReceive("encrypt")->with("record3_decrypted", "test_profile")->andReturn("record3_rotated");

        $store = Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreInterface");
        $store->shouldReceive("fetch")->once()->ordered()->with(0, 3)->andReturn(
            [
                $records[0],
                $records[1],
                $records[2],
            ]);
        $store->shouldReceive("save")->once()->ordered()->with(
            [
                $rotatedRecords[0],
                $rotatedRecords[1],
                $rotatedRecords[2],
            ]
        );
        $store->shouldReceive("fetch")->once()->ordered()->with(3, 3)->andReturn([$records[3]]);
        $store->shouldReceive("save")->once()->ordered()->with(
            [
                $rotatedRecords[3]
            ]
        );
        $store->shouldReceive("fetch")->once()->ordered()->with(6, 3)->andReturn([]);

        $storeRegistry = Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Store\\StoreRegistry");
        $storeRegistry
            ->shouldReceive("get")
            ->with("test_store")
            ->andReturn($store);

        $rotator = new Rotator($encryptor, $storeRegistry);
        $rotator->rotate("test_store", "test_profile", new Bounds(0, 9, 3), $observer);

        $storeRegistry->shouldHaveReceived("get")->with("test_store");

        $store->shouldHaveReceived("fetch")->withArgs([0, 3]);
        $store->shouldHaveReceived("fetch")->withArgs([3, 3]);
        $store->shouldHaveReceived("fetch")->withArgs([6, 3]);

        $encryptor->shouldHaveReceived("decrypt")->with("record0_encrypted");
        $encryptor->shouldHaveReceived("decrypt")->with("record1_encrypted");
        $encryptor->shouldHaveReceived("decrypt")->with("record2_encrypted");
        $encryptor->shouldHaveReceived("decrypt")->with("record3_encrypted");

        $encryptor->shouldHaveReceived("encrypt")->with("record0_decrypted", "test_profile");
        $encryptor->shouldHaveReceived("encrypt")->with("record1_decrypted", "test_profile");
        $encryptor->shouldHaveReceived("encrypt")->with("record2_decrypted", "test_profile");
        $encryptor->shouldHaveReceived("encrypt")->with("record3_decrypted", "test_profile");

        $store->shouldHaveReceived("save")->with([
            new Record(0, ['record0_rotated']),
            new Record(1, ['record1_rotated']),
            new Record(2, ['record2_rotated']),
        ]);

        $store->shouldHaveReceived("save")->with([
            new Record(3, ['record3_rotated']),
        ]);
    }
}
