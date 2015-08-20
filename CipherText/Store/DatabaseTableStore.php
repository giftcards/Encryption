<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/20/15
 * Time: 5:44 PM
 */

namespace Omni\Encryption\CipherText\Store;

use Omni\Encryption\Encryptor;

class DatabaseTableStore implements StoreInterface
{
    protected $pdo;
    protected $table;
    protected $fields;
    protected $idField;

    /**
     * DatabaseStore constructor.
     * @param $pdo
     * @param $table
     * @param $fields
     * @param $idField
     */
    public function __construct(\PDO $pdo, $table, array $fields, $idField)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->fields = $fields;
        $this->idField = $idField;
    }

    public function rotate(Encryptor $encryptor, $newProfile = null)
    {
        $fields = $this->fields;
        $fields[] = $this->idField;
        $stmt = $this->pdo->prepare(sprintf(
            'SELECT %s FROM %s',
            implode(', ', $fields),
            $this->table
        ));
        $stmt->execute();

        foreach ($stmt->fetch(\PDO::FETCH_ASSOC) as $row) {
            $id = $row[$this->idField];
            unset($row[$this->idField]);
            $encryptedRow = array_map(
                array($encryptor, 'encrypt'),
                array_map(array($encryptor, 'decrypt'), $row)
            );
            $parameters = array();
            $setFields = array_map(function ($field, $value) use (&$parameters) {
                $parameters[] = $value;
                return sprintf('%s = ?', $field);
            }, array_keys($encryptedRow), $encryptedRow);
            $this->pdo->prepare(sprintf(
                'UPDATE %s SET %s WHERE %s = ?',
                $this->table,
                implode(',', $setFields)
            ))->execute(array($id));
        }
    }
}
