<?php

namespace DemoShop\Application\Persistence\Encryption;

use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use DemoShop\Infrastructure\Exception\DecryptionException;
use DemoShop\Infrastructure\Exception\EncryptionException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
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
     *
     * @throws EncryptionException
     */
    public function encrypt(string $plainText): string
    {
        try {
            return $this->encrypter->encrypt($plainText);
        } catch (EncryptException $e) {
            throw new EncryptionException('Encryption failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Decrypts the given cipher text string.
     *
     * @param string $cipherText The encrypted data to decrypt.
     *
     * @return string The original plain text.
     * 
     * @throws DecryptionException
     */
    public function decrypt(string $cipherText): string
    {
        try {
            return $this->encrypter->decrypt($cipherText);
        } catch (DecryptException $e) {
            throw new DecryptionException("Failed to decrypt data.", 0, $e);
        }
    }
}