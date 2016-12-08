<?php
/**
 * Created by PhpStorm.
 * User: ydera00
 * Date: 12/8/16
 * Time: 2:13 PM
 */

namespace Giftcards\Encryption\Doctrine;

use Giftcards\Encryption\Encryptor;

class FieldEncryptor
{
    protected $encryptor;
    /**
     * @var FieldData[][]
     */
    protected $fieldCache = array();

    /**
     * FieldEncryptor constructor.
     * @param $encryptor
     */
    public function __construct(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    public function encryptField(
        $entity,
        \ReflectionProperty $field,
        $profile = null,
        array $ignoredValues = array(null)
    ) {
        $clearText = $field->getValue($entity);
        
        if (in_array($clearText, $ignoredValues, true)) {
            return;
        }

        $objectHash = spl_object_hash($entity);
        $fieldData = $this->getFieldData($field, $objectHash, $profile);

        if (!$fieldData || $fieldData->getClearText() !== $clearText) {
            $this->fieldCache[$objectHash][$field->getName()] = $fieldData = new FieldData(
                $clearText,
                $this->encryptor->encrypt(
                    $clearText,
                    $profile
                ),
                $profile
            );
        }

        $field->setValue($entity, $fieldData->getCipherText());
    }

    public function decryptField(
        $entity,
        \ReflectionProperty $field,
        $profile = null,
        array $ignoredValues = array(null)
    ) {
        $cipherText = $field->getValue($entity);

        if (in_array($cipherText, $ignoredValues, true)) {
            return;
        }

        $objectHash = spl_object_hash($entity);
        $fieldData = $this->getFieldData($field, $objectHash, $profile);

        if (!$fieldData || $fieldData->getCipherText() !== $cipherText) {
            $this->fieldCache[$objectHash][$field->getName()] = $fieldData = new FieldData(
                $this->encryptor->decrypt(
                    $cipherText,
                    $profile
                ),
                $cipherText,
                $profile
            );
        }

        $field->setValue($entity, $fieldData->getClearText());
    }

    public function clearFieldCache()
    {
        $this->fieldCache = array();
        return $this;
    }

    private function getFieldData(\ReflectionProperty $field, $objectHash, $profile)
    {
        $fieldData = isset($this->fieldCache[$objectHash][$field->getName()])
            ? $this->fieldCache[$objectHash][$field->getName()]
            : null
        ;
        return $fieldData && $fieldData->getProfile() === $profile ? $fieldData : null;
    }
}
