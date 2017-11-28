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
        $connection = \Mockery::mock("Doctrine\\DBAL\\Connection");
        assert($connection instanceof Connection);
        $table = $this->getFaker()->word;
        $fields = array(
            $this->getFaker()->word
        );
        $idField = $this->getFaker()->word;
        $builder = new DoctrineDBALStoreBuilder();
        $this->assertEquals(new DoctrineDBALStore($connection, $table, $fields, $idField), $builder->build(array(
            'connection' => $connection,
            'table' => $table,
            'fields' => $fields,
            'idField' => $idField
        )));
    }

    public function testResolver()
    {
        $builder = new DoctrineDBALStoreBuilder();
        $resolver = \Mockery::mock("Symfony\\Component\\OptionsResolver\\OptionsResolver");
        $resolver->shouldReceive("setRequired")->with(array(
            'connection',
            'table',
            'fields',
            'id_field'
        ))->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(array(
            "connection",
            "Doctrine\\DBAL\\Connection"
        ))->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(array("table", "string"))->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(array("fields", "array"))->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(array("id_field", "string"))->andReturnSelf();

        $builder->configureOptionsResolver($resolver);

        $resolver->shouldHaveReceived("setRequired")->with(array(
            'connection',
            'table',
            'fields',
            'id_field'
        ));
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(array("connection", "Doctrine\\DBAL\\Connection"));
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(array("table", "string"));
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(array("fields", "array"));
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(array("id_field", "string"));
    }
}
