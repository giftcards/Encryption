<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/25/15
 * Time: 2:03 PM
 */

namespace Giftcards\Encryption\Tests\CipherText\Rotator;

use Mockery\MockInterface;
use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputObserver;
use Omni\TestingBundle\TestCase\Extension\AbstractExtendableTestCase;

class ConsoleOutputObserverTest extends AbstractExtendableTestCase
{
    /** @var  ConsoleOutputObserver */
    protected $observer;
    /** @var  MockInterface */
    protected $consoleOutput;

    public function setUp()
    {
        $this->observer = new ConsoleOutputObserver(
            $this->consoleOutput = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface')
        );
    }

    public function testRotating()
    {
        $id = $this->getFaker()->unique()->word;
        $this->consoleOutput
            ->shouldReceive('writeln')
            ->once()
            ->with(sprintf('Rotating record with id "%s"', $id))
        ;
        $this->observer->rotating($id);
        $this->consoleOutput
            ->shouldReceive('writeln')
            ->once()
            ->with(sprintf('Rotated record with id "%s"', $id))
        ;
        $this->observer->rotated($id);
    }
}
