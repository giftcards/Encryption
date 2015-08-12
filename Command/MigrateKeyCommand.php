<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/3/15
 * Time: 6:10 PM
 */

namespace Omni\Encryption\Command;

use Omni\Encryption\EncryptedData\Data;
use Omni\Encryption\EncryptedData\StoreRegistry;
use Omni\Encryption\Encryptor\EncryptorRegistry;
use Omni\Encryption\Key\SourceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateKeyCommand extends Command
{
    protected $keySource;
    protected $storeRegistry;
    protected $encrypterRegistry;

    public function __construct(
        SourceInterface $keySource,
        StoreRegistry $storeRegistry,
        EncryptorRegistry $encrypterRegistry
    ) {
        $this->keySource = $keySource;
        $this->storeRegistry = $storeRegistry;
        $this->encrypterRegistry = $encrypterRegistry;
        parent::__construct('keys:migrate');
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->addArgument('new-key-name', InputArgument::REQUIRED, 'The new key the current data is encrypted with.')
            ->addArgument('encrypter', InputArgument::REQUIRED, 'The encrypter to use.')
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
        $encrypter = $this->encrypterRegistry->get($input->getArgument('encrypter'));
        $newKey = $this->keySource->get($input->getArgument('new-key-name'));

        foreach ($input->getArgument('stores') as $storeName) {
            $store = $this->storeRegistry->get($storeName);
            foreach ($store->load($input->getOption('store-option')) as $encryptedData) {
                $data = $encryptedData->getData();
                $oldKey = $encryptedData->getKeyName();
                if (!$input->getOption('no-decrypt')) {
                    $data = array_map(function ($value) use ($encrypter, $oldKey) {
                        return $encrypter->decrypt($value, $oldKey);
                    }, $data);
                }
                if (!$input->getOption('no-encrypt')) {
                    $data = array_map(function ($value) use ($encrypter, $newKey) {
                        return $encrypter->encrypt($value, $newKey);
                    }, $data);
                }
                $store->save(new Data($encryptedData->getId(), $data, $newKey));
            }
        }
    }
}
