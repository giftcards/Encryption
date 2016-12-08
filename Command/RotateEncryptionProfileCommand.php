<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:10 PM
 */

namespace Giftcards\Encryption\Command;

use Giftcards\Encryption\CipherText\CipherText;
use Giftcards\Encryption\CipherText\Group;
use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputObserver;
use Giftcards\Encryption\CipherText\Rotator\RotatorRegistry;
use Giftcards\Encryption\Encryptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RotateEncryptionProfileCommand extends Command
{
    protected $rotatorRegistry;
    protected $encryptor;

    public function __construct(
        RotatorRegistry $storeRegistry,
        Encryptor $cipherTextGenerator
    ) {
        $this->rotatorRegistry = $storeRegistry;
        $this->encryptor = $cipherTextGenerator;
        parent::__construct('encryption_profile:rotate');
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->addArgument('stores', InputArgument::IS_ARRAY|InputArgument::REQUIRED, 'A list of stores to re-encrypt.')
            ->addOption(
                'new-profile',
                null,
                InputOption::VALUE_REQUIRED,
                'The new profile to encrypt the data with.',
                null
            )
            ->addOption(
                'old-profile',
                null,
                InputOption::VALUE_REQUIRED,
                'The profile the current data is encrypted with.',
                null
            )
            ->addOption(
                'starting-id',
                null,
                InputOption::VALUE_REQUIRED,
                'The id in the store to start in.',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newProfile = $input->getOption('new-profile');
        $oldProfile = $input->getOption('new-profile');
        $startingId = $input->getOption('new-profile');
        $observer = new ConsoleOutputObserver($output);

        foreach ($input->getArgument('stores') as $storeName) {
            $store = $this->rotatorRegistry->get($storeName);
            $store->rotate(
                $observer,
                $this->encryptor,
                $newProfile
            );
        }
    }
}
