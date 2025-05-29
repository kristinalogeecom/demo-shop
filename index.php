<?php

require_once __DIR__ . '/vendor/autoload.php';

use DemoShop\Application\Configuration\App;

try {
    App::boot();
} catch (Exception $e) {
    echo $e->getMessage();
}

