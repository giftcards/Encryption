<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/20/15
 * Time: 7:14 PM
 */

namespace Omni\Encryption\CipherText\Rotator;

use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputObserver implements ObserverInterface
{
    protected $output;

    /**
     * ConsoleOutputObserver constructor.
     * @param $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function rotating($id)
    {
        $this->output->writeln(sprintf('Rotating record with id "%s"', $id));
    }

    public function rotated($id)
    {
        $this->output->writeln(sprintf('Rotated record with id "%s"', $id));
    }
}
