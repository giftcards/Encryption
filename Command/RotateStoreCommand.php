<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:10 PM
 */

namespace Giftcards\Encryption\Command;

use Giftcards\Encryption\CipherText\Rotator\Bounds;
use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputRotatorObserver;
use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Giftcards\Encryption\CipherText\Rotator\RotatorObserverChain;
use Giftcards\Encryption\CipherText\Rotator\Tracker\TrackerInterface;
use Giftcards\Encryption\CipherText\Rotator\Tracker\TrackingObserver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RotateStoreCommand extends Command
{
    /**
     * @var Rotator
     */
    private $rotator;

    /**
     * @var TrackerInterface
     */
    private $tracker;

    public function __construct(Rotator $rotator, TrackerInterface $tracker)
    {
        $this->rotator = $rotator;
        parent::__construct('giftcards_encryption:stores:rotate');
        $this->tracker = $tracker;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->addArgument('stores', InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'A list of stores to re-encrypt.')
            ->addOption(
                'new-profile',
                null,
                InputArgument::REQUIRED,
                'The new profile the current data is encrypted with.',
                null
            )
            ->addOption(
                'limit',
                null,
                InputArgument::REQUIRED,
                'Max records to process',
                null
            )
            ->addOption(
                'batch-size',
                null,
                InputArgument::REQUIRED,
                'Records per batch to process',
                1
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getArgument('stores') as $storeName) {
            $offset = $this->tracker->get($storeName);
            $limit = $input->getOption('limit') - $offset;
            $this->rotator->rotate(
                $storeName,
                $input->getOption('new-profile'),
                new Bounds(
                    $offset,
                    $limit,
                    $input->getOption('batch-size')
                ),
                new RotatorObserverChain(
                    new TrackingObserver($this->tracker, $storeName),
                    new ConsoleOutputRotatorObserver($output)
                )
            );
            $this->tracker->reset($storeName);
        }
    }
}
