<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:10 PM
 */

namespace Omni\Encryption\Command;

use Omni\Encryption\CipherText\CipherText;
use Omni\Encryption\CipherText\Group;
use Omni\Encryption\CipherText\Store\StoreRegistry;
use Omni\Encryption\Encryptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateKeyCommand extends Command
{
    protected $storeRegistry;
    protected $encryptor;

    public function __construct(
        StoreRegistry $storeRegistry,
        Encryptor $cipherTextGenerator
    ) {
        $this->storeRegistry = $storeRegistry;
        $this->encryptor = $cipherTextGenerator;
        parent::__construct('keys:migrate');
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
                'The new profile the current data is encrypted with.',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newProfile = $input->getArgument('new-profile');

        foreach ($input->getArgument('stores') as $storeName) {
            $store = $this->storeRegistry->get($storeName);
            $store->rotate($this->encryptor, $newProfile);
        }
    }
}
