<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 4:18 PM
 */

namespace Giftcards\Encryption\Tests\Command;

use Giftcards\Encryption\CipherText\Rotator\Bounds;
use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputRotatorObserver;
use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Giftcards\Encryption\CipherText\Rotator\RotatorObserverChain;
use Giftcards\Encryption\Command\RotateRangeInStoreCommand;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class RotateRangeTest extends AbstractTestCase
{

    public function testCommand()
    {
        $storeName = $this->getFaker()->unique()->word();
        $newProfile = $this->getFaker()->unique()->word();

        $rotator = \Mockery::mock(Rotator::class);
        $rotator->shouldReceive("rotate");
        assert($rotator instanceof Rotator);

        $offset = $this->getFaker()->unique()->randomNumber();
        $limit = $this->getFaker()->unique()->randomNumber();
        $batchSize = $this->getFaker()->unique()->randomNumber();

        $command = new RotateRangeInStoreCommand($rotator);
        $input = new StringInput("{$storeName} --offset={$offset} --limit={$limit} --batch-size={$batchSize} --new-profile={$newProfile}");
        $output = new ConsoleOutput();
        $command->run($input, $output);

        $rotator->shouldHaveReceived("rotate")->withArgs([
            $storeName,
            $newProfile,
            \Hamcrest_Matchers::equalTo(new Bounds($offset, $limit, $batchSize)),
            \Hamcrest_Matchers::equalTo(new RotatorObserverChain(
                new ConsoleOutputRotatorObserver($output)
            ))
        ]);
    }

}