<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/4/15
 * Time: 5:42 PM
 */

namespace Omni\Encryption\Key;

use Doctrine\MongoDB\Connection;

class MongoSource extends AbstractSource
{
    protected $connection;
    protected $database;
    protected $collection;
    protected $findByField;
    protected $valueField;

    /**
     * MongoSource constructor.
     * @param Connection $connection
     * @param $database
     * @param $collection
     * @param string $findByField
     * @param string $valueField
     */
    public function __construct(
        Connection $connection,
        $database,
        $collection,
        $findByField = 'name',
        $valueField = 'value'
    ) {
        $this->connection = $connection;
        $this->database = $database;
        $this->collection = $collection;
        $this->findByField = $findByField;
        $this->valueField = $valueField;
    }

    public function has($key)
    {
        return (bool) $this->connection->selectCollection($this->database, $this->collection)
            ->findOne(array($this->findByField => $key))
        ;
    }

    protected function getKey($key)
    {
        $record = $this->connection->selectCollection($this->database, $this->collection)
            ->findOne(array($this->findByField => $key))
        ;
        return $record[$this->valueField];
    }
}
