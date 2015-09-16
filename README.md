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
    ->addKeySource('array', array('keys' => array('foo' => 'bar', 'none' => '')) //array source just takes the array of keys you give it and uses
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

If you dont set the default profile you will need to pass the name fo the profile always when
encrypting. when decrypting however the same requirements will apply since it will do the same
work trying to figure out what profile it was encrypted with.

Diving Deeper
-------------

### Ciphers ###
Ciphers are classes following the `Giftcards\Encryption\Cipher\CipherInterface` they are how you
can define different ways to encrypt/decrypt data. The current implementations are

- mysql_aes - this one duplicates the AES_ENCRYPT/AES_DECRYPT functions in mysql/mariadb
- no_op - this one is just a passthrough it will just return the data exactly the same as given on encrypt/decrypt

### Key Sources ###
Key sources are classes following the `Giftcards\Encryption\Key\SourceInterface` they are how you can
define ways for the encryptor to load a key they take the key by name and return the key if found
and if not they throw an exception. the current implementations are

- ArraySource - this one takes an array of keys as name value pairs
- CachingSource - this one takes another source into it and caches the requests for keys
- ChainSource - takes a chain of key sources and returns the key for the first source to have a value for the given name
- CombiningSource - this source allows you to make a new key out of 2 other keys it takes an array with the names of the new
                    keys and the values for each being the names of keys to use as the left and right part of this new one
- ContainerAwareChainSource - this one works the same as the ChainSource, however it takes a symfony container and allows you
                              to set the names of services as ket sources to allow for them to only be loaded when requests for
                              keys are made
- ContainerParametersSource - this one takes a symfony container and looks for parameters named the same as the key requested
- FallbackSource - this one takes an array of key names and an array of fallbacks if they dont exist and goes through all the fallbacks before throwing
                   an exception
- IniFileSource - this one takes the path to an ini file and loads keys using the keys of the ini file
- MappingSource - this one allows you to alias keys. it takes an array with the new key names as the keys and the keys to map to as the values
- MongoSource - this source allows you to define a mongo collection as a key source. It uses a search field you define to search for a key by the name given
- NoneSource - this is jsut an easy way of adding a key called `none`
- PrefixKeyNameSource - this one allows you to wrap a different source and define a prefix for it. This can be really useful if you use the chain. you can wrap
                        for example the mongo source and say that it should only response to keys prefixed with `mongo:` the colon is the default
                        separator and is configurable
- VaultSource - this allows you to define a source that pulls your keys from vault

### Profiles ###
Profiles are a way of grouping a cipher with a key name. The encryptor works wth profiles to decide how to encrypt/decrypt data
all it contains is the name of a key and the name of a cipher to use

### Cipher Text ###
Cipher Text are ment to represent data that has been encrypted. The encryptor returns an instance of this that is stringable
and will automatically serialize using the serializers mentioned later on if you cast it to a string. The idea is to allow for the
encrypted data and the profile used to encrypt it to move together

#### Cipher Text Serializers/Deserializers ####

The cipher text serializers and deserializers are in charge of making a cipher text instance into a string for easy storage
and deserializing them on the way out before decryption in the encryptor. this allows for them to be stored easily and the encryptor
to be able to deal just with cipher texts with all the info on how to decrypt them. There are 2 interfaces 
`Giftcards\Encryption\CipherText\Serializer\SerializerInterface` and `Giftcards\Encryption\CipherText\Serializer\DeserializerInterface`
there is a combination interface called `SerializerDeserializerInterface` which the encryptor actually uses. Separating the interfaces allows
for different ways ofr serializing and deserializing.

