<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\Persistence\Model\Admin;

class AdminRepository
{
    public function findByUsername(string $username): ?Admin
    {
        return Admin::where('username', $username)->first();
    }
}