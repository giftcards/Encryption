Omni Encryption Library
-----------------------

Setup
-----

Add the following to your composer.json:

    "omni/encryption": "~1.0",

Then do a `composer update`.

Usage
-----

Simple usage:

    namespace Omni\Namespace;

    use Omni\Encryption\EncryptionUtility;
    // ....

    class SomeClass
    {
        protected $encryptionUtility;
        // ...

        public function __construct($encryptionKey)
        {
            // NOTE: Encryption key should come from an INI file -- never check keys into source control
            $this->encryptionUtility = new EncryptionUtility($encryptionKey);
            //...
        }

        public function encodeMessage($message)
        {
            return $this->encryptionUtility->aesEncrypt($message);
        }

        public function decodeMessage($message)
        {
            return $this->encryptionUtility->aesDecrypt($message);
        }
    }

Dependency Injection:

  *services.yml* (or similar file)

    omni.encryption.encryption_utility:
        class: Omni\EncryptionBundle\EncryptionUtility
        arguments: ['%ENCRYPTION_STRING%']

    omni.some.service.class:
        class: Omni\Namespace\SomeClass
        arguments: [@omni.encryption.encryption_utility]

  *Omni/Service/SomeClass.php*

    namespace Omni\Namespace;

    use Omni\EncryptionBundle\EncryptionUtility;

    class SomeClass
    {
        protected $encryptionUtility;

        public function __construct(EncryptionUtility $encryptionUtility)
        {
            $this->encryptionUtility = $encryptionUtility;
        }

        // etc...
    }


The EncryptionUtility class provides the following functions:

  * `aesEncrypt($data)` -- returns the $data encrypted with 128-bit Rijndael encryption
  * `aesDecrypt($data)` -- returns the decrypted, or null if the original data was not encrypted with the same key and algorithm

