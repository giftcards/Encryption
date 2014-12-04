Omni Encryption Library
-----------------------

Setup
-----

Add the following to your composer.json:

    "omni/encryption": "~1.0",

Then do a `composer update`.

Usage
-----

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

The EncryptionUtility class provides the following functions:

  * `aesEncrypt($data)` -- returns the $data encrypted with 128-bit Rijndael encryption
  * `aesDecrypt($data)` -- returns the decrypted, or null if the original data was not encrypted with the same key and algorithm
  * `checkPasswordFormat($password)` -- returns true if the password is at least 8 characters, with at least one digit and at least one uppercase letter

