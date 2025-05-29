<?php

namespace DemoShop\Application\BusinessLogic\Model;

/**
 * Represents an admin user in the system.
 */
class Admin
{
    private int $id;
    private string $username;
    private string $password;
    private bool $rememberMe;

    /**
     * @param string $username
     * @param string $password
     * @param bool $rememberMe
     */
    public function __construct(string $username, string $password, bool $rememberMe)
    {
        $this->username = $username;
        $this->password = $password;
        $this->rememberMe = $rememberMe;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }

    /**
     * @param bool $rememberMe
     * @return void
     */
    public function setRememberMe(bool $rememberMe): void
    {
        $this->rememberMe = $rememberMe;
    }

}