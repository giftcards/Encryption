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
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class DoctrineDBALStoreBuilderTest extends AbstractExtendableTestCase
{
    public function testBuilder()
    {
        $connection = Mockery::mock("Doctrine\\DBAL\\Connection");
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

    public function testResolver()
    {
        $builder = new DoctrineDBALStoreBuilder();
        $resolver = Mockery::mock("Symfony\\Component\\OptionsResolver\\OptionsResolver");
        $resolver->shouldReceive("setRequired")->with([
            'connection',
            'table',
            'fields',
            'id_field'
        ])->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs([
            "connection",
            "Doctrine\\DBAL\\Connection"
        ])->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(["table", "string"])->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(["fields", "array"])->andReturnSelf();
        $resolver->shouldReceive("setAllowedTypes")->withArgs(["id_field", "string"])->andReturnSelf();

        $builder->configureOptionsResolver($resolver);

        $resolver->shouldHaveReceived("setRequired")->with([
            'connection',
            'table',
            'fields',
            'id_field'
        ]);
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(["connection", "Doctrine\\DBAL\\Connection"]);
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(["table", "string"]);
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(["fields", "array"]);
        $resolver->shouldHaveReceived("setAllowedTypes")->withArgs(["id_field", "string"]);
    }
}
