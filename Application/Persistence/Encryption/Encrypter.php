<?php

namespace DemoShop\Application\Persistence\Encryption;

use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use Illuminate\Encryption\Encrypter as IlluminateEncrypter;

/**
 * Concrete implementation of the EncryptionInterface.
 * Wraps Laravel's Encrypter to provide AES-256-CBC encryption and decryption.
 */
class Encrypter implements EncryptionInterface
{
    private IlluminateEncrypter $encrypter;

    /**
     * Initializes the internal Laravel Encrypter instance using the provided base64-encoded key.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->encrypter = new IlluminateEncrypter(
            base64_decode(str_replace('base64:', '', $key)),
            'AES-256-CBC'
        );
    }

    /**
     * Encrypts the given plain text string.
     *
     * @param string $plainText The data to encrypt.
     *
     * @return string The encrypted (cipher) text.
     */
    public function encrypt(string $plainText): string
    {
        return $this->encrypter->encrypt($plainText);
    }

    /**
     * Decrypts the given cipher text string.
     *
     * @param string $cipherText The encrypted data to decrypt.
     *
     * @return string The original plain text.
     */
    public function decrypt(string $cipherText): string
    {
        return $this->encrypter->decrypt($cipherText);
    }
}