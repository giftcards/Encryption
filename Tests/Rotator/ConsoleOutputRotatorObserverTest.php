<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/21/17
 * Time: 4:26 PM
 */

namespace Giftcards\Encryption\Tests\Rotator;

use Giftcards\Encryption\CipherText\Rotator\ConsoleOutputRotatorObserver;
use Giftcards\Encryption\CipherText\Rotator\Record;
use Giftcards\Encryption\Tests\AbstractTestCase;

class ConsoleOutputRotatorObserverTest extends AbstractTestCase
{

    public function testObserver()
    {
        $output = \Mockery::mock("Symfony\\Component\\Console\\Output\\OutputInterface");
        $output->shouldReceive("writeln");

        $offset = $this->getFaker()->unique()->randomNumber();
        $limit = $this->getFaker()->unique()->randomNumber();
        $record = new Record($this->getFaker()->unique()->randomNumber(), array(
            'value' => $this->getFaker()->unique()->word()
        ));
        $records = array($record);

        $observer = new ConsoleOutputRotatorObserver($output);

        $observer->fetchedRecords($offset, $limit, $records);
        $output->shouldHaveReceived("writeln")->with(
            sprintf(
                "Fetching records %d-%d, %d found",
                $offset,
                $offset + $limit,
                count($records)
            )
        );

        $observer->rotatingRecord($record);
        $output->shouldHaveReceived("writeln")->with(
            sprintf("Rotating record with ID '%d'", $record->getId())
        );

        $observer->rotatedRecord($record);
        $output->shouldHaveReceived("writeln")->with(
            sprintf("Rotated record with ID '%d'", $record->getId())
        );

        $observer->savedRecords($offset, $limit, $records);
        $output->shouldHaveReceived("writeln")->with(
            sprintf("Saving records %d-%d, %d total", $offset, $offset + $limit, count($records))
        );
    }

}