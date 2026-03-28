<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

$entityManager = require __DIR__ . '/bootstrap/doctrine.php';

ConsoleRunner::run(new SingleManagerProvider($entityManager));
