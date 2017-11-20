<?php
/**
 * Created by PhpStorm.
 * User: jjose00
 * Date: 11/7/17
 * Time: 8:22 AM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

class Bounds implements \Iterator
{
    /**
     * @var int
     */
    private $offset;
    /**
     * @var null
     */
    private $limit;
    /**
     * @var int
     */
    private $batchSize;
    /**
     * @var int
     */
    private $key = 0;

    /**
     * @var int
     */
    private $currentOffset;

    /**
     * @var int
     */
    private $currentLimit;

    /**
     * Bounds constructor.
     * @param int $offset Starting record number
     * @param null $limit Max amount of records to process
     * @param int $batchSize Amount of records per batch
     */
    public function __construct($offset = 0, $limit = null, $batchSize = 1)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->batchSize = $batchSize;
    }

    public function key()
    {
        return $this->key;
    }

    public function next()
    {
        $this->key++;
        $this->calculateBounds();
    }

    public function valid()
    {
        return ($this->limit == null) || ($this->limit > $this->currentOffset);
    }

    public function current()
    {
        return array($this->currentOffset, $this->currentLimit);
    }

    public function rewind()
    {
        $this->key = 0;
        $this->calculateBounds();
    }

    private function calculateBounds()
    {
        $this->currentOffset = $this->offset + ($this->batchSize * $this->key());
        $this->currentLimit = ($this->limit === null) ? $this->batchSize :
            min($this->limit - $this->currentOffset, $this->batchSize);
    }
}