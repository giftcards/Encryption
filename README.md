Giftcards Encryption Library [![Build Status](https://travis-ci.org/giftcards/Encryption.svg?branch=master)](https://travis-ci.org/giftcards/Encryption)
----------------------------

Library that helps make configuring and using encrypt schemes easier.

Purpose
-------
This library was made to make managing encryptoin of data eaiser including sourcing encryption keys,
rotating keys, using different ciphers, and being able to better keep track of wich data was encrypted with 
combination of keys

Usage
-----

### Basic ###

to just get going you will need to define at least one key source and one profile.

profiles are the way you tell the encryptor which cipher you want to use and which
key you want to use for encrypting/decrypting and key sources are the classes
that are in charge of loading a key given its name.

simple example.

```php
<?php

use Giftcards\Encryption\EncryptorBuilder;

$encryptor = EncryptorBuilder::newInstance()
    ->addKeySource('array', array('keys' => array('foo' => 'bar', 'none' => '')) //array source jsut takes the array of keys you give it and uses
                                                                   //that to return the key on request
    ->setProfile('default', 'foo', 'mysql_aes')
    ->setProfile('no_op', 'none', 'no_op')
    ->setDefaultProfile('default')
    ->build()
;

$encrypted1 = $encryptor->encrypt('baz');//this will use the default profile
$encrypted2 = $encryptor->encrypt('baz', 'default'); //this will too
$encrypted3 = $encryptor->encrypt('baz', 'no_op'); //this will use the no_op profile
$decrypted1 = $encryptor->decrypt($encrypted1); //the encrypted data is actually an object
                                               //which can be cast to a string
                                              //that holds the encrypted text plus the profile 
                                              //used so you dont need to put the profile
$decrypted1 = $encryptor->decrypt($encrypted2, 'default');//you can also tell the encryptor which profile you want to use
$decrypted1 = $encryptor->decrypt($encrypted3, 'default');//you could even tell it to use a different profile than what was used
                                                          //to encrypt the data

```

