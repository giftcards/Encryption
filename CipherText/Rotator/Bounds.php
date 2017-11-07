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
    private $lowerBound;

    /**
     * @var int
     */
    private $upperBound;

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
        $this->calculateBounds();
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
        return ($this->limit == null) || ($this->limit > $this->lowerBound);
    }

    public function current()
    {
        return [$this->lowerBound, $this->upperBound];
    }

    public function rewind()
    {
        $this->key = 0;
    }

    private function calculateBounds()
    {
        $this->lowerBound = $this->offset + ($this->batchSize * $this->key());
        $this->upperBound = $this->batchSize;
        if ($this->limit == null) {
            return;
        }
        if ($this->lowerBound + $this->upperBound > $this->limit) {
            $this->upperBound = $this->limit - $this->lowerBound;
        }
    }
}