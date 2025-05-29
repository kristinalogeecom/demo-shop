<?php

namespace DemoShop\Application\BusinessLogic\Encryption;

/**
 * Ensures consistent encryption and decryption methods
 * across different implementations.
 */
interface EncryptionInterface
{
    /**
     * Encrypts the given plain text string.
     *
     * @param string $plainText
     *
     * @return string
     */
    public function encrypt(string $plainText): string;

    /**
     * Decrypts the given cipher text string.
     *
     * @param string $cipherText
     *
     * @return string
     */
    public function decrypt(string $cipherText): string;
}