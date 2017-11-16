<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 10:16 AM
 */

namespace Giftcards\Encryption\Tests\Rotator\Builders;

use Doctrine\DBAL\Connection;
use Giftcards\Encryption\CipherText\Rotator\Store\DoctrineDBALStore;
use Giftcards\Encryption\CipherText\Rotator\Store\DoctrineDBALStoreBuilder;
use Giftcards\Encryption\Tests\AbstractTestCase;

class DoctrineDBALStoreBuilderTest extends AbstractTestCase
{
    public function testBuilder()
    {
        $connection = \Mockery::mock(Connection::class);
        assert($connection instanceof Connection);
        $table = $this->getFaker()->word;
        $fields = [
            $this->getFaker()->word
        ];
        $idField = $this->getFaker()->word;
        $builder = new DoctrineDBALStoreBuilder();
        $this->assertEquals(new DoctrineDBALStore($connection, $table, $fields, $idField), $builder->build([
            'connection' => $connection,
            'table' => $table,
            'fields' => $fields,
            'idField' => $idField
        ]));
    }
}