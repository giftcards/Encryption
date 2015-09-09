<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/20/15
 * Time: 5:44 PM
 */

namespace Giftcards\Encryption\CipherText\Rotator;

use Giftcards\Encryption\Encryptor;

class DatabaseTableRotator implements RotatorInterface
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

    public function rotate(ObserverInterface $observer, Encryptor $encryptor, $newProfile = null)
    {
        $fields = $this->fields;
        $fields[] = $this->idField;
        $stmt = $this->pdo->prepare(sprintf(
            'SELECT %s FROM %s',
            implode(', ', $fields),
            $this->table
        ));
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row[$this->idField];
            $observer->rotating($id);
            unset($row[$this->idField]);
            foreach ($row as $key => $value) {
                $row[$key] = $encryptor->encrypt($encryptor->decrypt($value), $newProfile);
            }
            $parameters = array();
            $setFields = array_map(function ($field, $value) use (&$parameters) {
                $parameters[] = $value;
                return sprintf('%s = ?', $field);
            }, array_keys($row), $row);
            $parameters[] = $id;
            $this->pdo->prepare(sprintf(
                'UPDATE %s SET %s WHERE %s = ?',
                $this->table,
                implode(',', $setFields),
                $this->idField
            ))->execute($parameters);
            $observer->rotated($id);
        }
    }
}
