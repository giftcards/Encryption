<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/16/17
 * Time: 4:18 PM
 */

namespace Giftcards\Encryption\Tests\Command;

use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Giftcards\Encryption\CipherText\Rotator\Tracker\TrackerInterface;
use Giftcards\Encryption\Command\RotateStoreCommand;
use Giftcards\Encryption\Tests\AbstractTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class RotateStoreTest extends AbstractTestCase
{

    public function testCommand()
    {
        $storeName = $this->getFaker()->unique()->word();
        $newProfile = $this->getFaker()->unique()->word();

        $rotator = \Mockery::mock(Rotator::class);
        $rotator->shouldReceive("rotate");
        assert($rotator instanceof Rotator);

        $tracker = \Mockery::mock(TrackerInterface::class);
        $tracker->shouldReceive("get")->andReturn(0);
        $tracker->shouldReceive("reset");
        assert($tracker instanceof TrackerInterface);

        $command = new RotateStoreCommand($rotator, $tracker);
        $input = new StringInput("{$storeName} --limit=350 --new-profile={$newProfile}");
        $output = new ConsoleOutput();
        $command->run($input, $output);

        $tracker->shouldHaveReceived("get")->with($storeName);
        $tracker->shouldHaveReceived("reset")->with($storeName);

        $rotator->shouldHaveReceived("rotate");
    }

}