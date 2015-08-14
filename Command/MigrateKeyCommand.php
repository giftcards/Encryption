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
use Omni\Encryption\CipherText\StoreRegistry;
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
            ->addArgument('new-profile', InputArgument::REQUIRED, 'The new key the current data is encrypted with.')
            ->addArgument('stores', InputArgument::IS_ARRAY|InputArgument::REQUIRED, 'A list of stores to re-encrypt.')
            ->addOption(
                'no-decrypt',
                null,
                InputOption::VALUE_NONE,
                'Use this option if you dont want the old data decrypted first'
            )
            ->addOption(
                'no-encrypt',
                null,
                InputOption::VALUE_NONE,
                'Use this option if you dont want the old data encrypted after'
            )
            ->addOption(
                'store-option',
                null,
                InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY,
                'Options to pass to the source loader',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newProfile = $input->getArgument('new-profile');
        $encryptor = $this->encryptor;

        foreach ($input->getArgument('stores') as $storeName) {
            $store = $this->storeRegistry->get($storeName);
            foreach ($store->load($input->getOption('store-option')) as $group) {
                $cipherTexts = array_map(function (CipherText $cipherText) use ($encryptor, $newProfile) {
                    return $encryptor->encrypt(
                        $this->encryptor->decrypt($cipherText),
                        $newProfile
                    );
                }, $group->getCipherTexts());
                $store->save(new Group($group->getId(), $cipherTexts));
            }
        }
    }
}
