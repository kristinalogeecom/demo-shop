<?php

namespace DemoShop\Application\BusinessLogic\RepositoryInterface;

interface ProductRepositoryInterface
{
    public function getAllProducts(): array;
}