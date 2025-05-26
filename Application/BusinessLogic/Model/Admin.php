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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }

    public function setRememberMe(bool $rememberMe): void
    {
        $this->rememberMe = $rememberMe;
    }

}