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
    private $batch;

    /**
     * @var int
     */
    private $key;

    /**
     * Bounds constructor.
     * @param int $offset Starting batch number
     * @param null $limit Max amount of records to process
     * @param int $batch Amount of records per batch
     */
    public function __construct($offset = 0, $limit = NULL, $batch = 1)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->batch = $batch;
        $this->key = $offset;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return int[] Array of two integers in the format [ $offset , $limit ]
     * @since 5.0.0
     */
    public function current()
    {
        return [
            ( $this->key * $this->batch ),
            $this->batch
        ];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->key++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return int|null Current key on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return ( $this->limit == null ) || ( $this->key * $this->batch < $this->limit );
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->key = $this->offset;
    }
}