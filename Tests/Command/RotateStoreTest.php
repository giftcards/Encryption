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
use Giftcards\Encryption\CipherText\Rotator\Tracker\TrackerInterface;
use Giftcards\Encryption\CipherText\Rotator\Tracker\TrackingObserver;
use Giftcards\Encryption\Command\RotateStoreCommand;

use Hamcrest\Matchers;
use Mockery;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class RotateStoreTest extends AbstractExtendableTestCase
{

    public function testCommand()
    {
        $storeName = $this->getFaker()->unique()->word();
        $newProfile = $this->getFaker()->unique()->word();

        $limit = $this->getFaker()->unique()->randomNumber();
        $batchSize = $this->getFaker()->unique()->randomNumber();

        $rotator = Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Rotator");
        $rotator->shouldReceive("rotate");
        assert($rotator instanceof Rotator);

        $tracker = Mockery::mock("Giftcards\\Encryption\\CipherText\\Rotator\\Tracker\\TrackerInterface");
        $tracker->shouldReceive("get")->andReturn(0);
        $tracker->shouldReceive("reset");
        assert($tracker instanceof TrackerInterface);

        $command = new RotateStoreCommand($rotator, $tracker);
        $input = new StringInput("{$storeName} --limit={$limit} --batch-size={$batchSize} --new-profile={$newProfile}");
        $output = new ConsoleOutput();
        $command->run($input, $output);

        $tracker->shouldHaveReceived("get")->with($storeName);
        $tracker->shouldHaveReceived("reset")->with($storeName);

        $rotator->shouldHaveReceived("rotate")->withArgs([
            $storeName,
            $newProfile,
            Matchers::equalTo(new Bounds(0, $limit, $batchSize)),
            Matchers::equalTo(new RotatorObserverChain(
                new TrackingObserver($tracker, $storeName),
                new ConsoleOutputRotatorObserver($output)
            ))
        ]);
    }
}
