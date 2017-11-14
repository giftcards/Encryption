<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/13/17
 * Time: 1:44 PM
 */

namespace Giftcards\Encryption\Command;

use Giftcards\Encryption\CipherText\Rotator\Bounds;
use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputRotatorObserver;
use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Giftcards\Encryption\CipherText\Rotator\RotatorObserverChain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RotateRangeInStoreCommand extends Command
{

    /**
     * @var Rotator
     */
    private $rotator;

    public function __construct(Rotator $rotator)
    {
        $this->rotator = $rotator;
        parent::__construct("giftcards_encryption:stores:rotate_range");
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
                InputOption::VALUE_REQUIRED,
                'The new profile the current data is encrypted with.',
                null
            )
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_OPTIONAL,
                'Starting record',
                0
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'Max records to process',
                null
            )
            ->addOption(
                'batch-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Records per batch to process',
                1
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newProfile = $input->getOption('new-profile');
        $offset = $input->getOption('offset');
        $limit = $input->getOption('limit');
        $batchSize = $input->getOption('batch-size');

        foreach ($input->getArgument('stores') as $storeName) {
            $this->rotator->rotate(
                $storeName,
                $newProfile,
                new Bounds(
                    $offset,
                    $limit,
                    $batchSize
                ),
                new RotatorObserverChain(
                    new ConsoleOutputRotatorObserver($output)
                )
            );
        }
    }

}