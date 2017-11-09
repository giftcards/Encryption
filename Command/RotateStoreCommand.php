<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:10 PM
 */

namespace Giftcards\Encryption\Command;

use Giftcards\Encryption\CipherText\Group;
use Giftcards\Encryption\CipherText\Rotator\Bounds;
use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputObserver;
use Giftcards\Encryption\CipherText\Rotator\Rotator;
use Giftcards\Encryption\CipherText\Rotator\StoreRegistry;
use Giftcards\Encryption\Encryptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RotateStoreCommand extends Command
{
    private $rotator;

    public function __construct(
        StoreRegistry $storeRegistry,
        Encryptor $cipherTextGenerator
    ) {
        $this->rotator = new Rotator($cipherTextGenerator, $storeRegistry);
        parent::__construct('giftcards_encryption:stores:rotate');
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
        $bounds = new Bounds(
            $input->getOption('offset'),
            $input->getOption('limit'),
            $input->getOption('batch-size')
        );
        $observer = new ConsoleOutputObserver($output);

        foreach ($input->getArgument('stores') as $storeName) {
            $this->rotator->rotate($storeName, $newProfile, $bounds, $observer);
        }
    }
}
