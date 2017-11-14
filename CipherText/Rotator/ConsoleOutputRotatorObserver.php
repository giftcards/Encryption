<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/10/17
 * Time: 4:12 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputRotatorObserver implements RotatorObserverInterface
{
    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function fetchedRecords($offset, $limit, array $records)
    {
        $this->output->writeln(sprintf("Fetching records %d-%d, %d found", $offset, $offset + $limit, count($records)));
    }

    public function rotatingRecord(Record $record)
    {
        $this->output->writeln(sprintf("Rotating record with ID '%d'", $record->getId()));
    }

    public function rotatedRecord(Record $record)
    {
        $this->output->writeln(sprintf("Rotated record with ID '%d'", $record->getId()));
    }

    public function savedRecords($offset, $limit, array $records)
    {
        $this->output->writeln(sprintf("Saving records %d-%d, %d total", $offset, $offset + $limit, count($records)));
    }
}