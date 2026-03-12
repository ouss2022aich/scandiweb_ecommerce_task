<?php

return [
    "driver" => $_ENV["DB_DRIVER"] ?? "pdo_mysql",
    "host" => $_ENV["DB_HOST"] ?? "mysql",
    "port" => (int)($_ENV["DB_PORT"] ?? 3306),
    "dbname" => $_ENV["DB_NAME"] ?? "scandiweb_db",
    "user" => $_ENV["DB_USER"] ?? "scandiweb_user",
    "password" => $_ENV["DB_PASS"] ?? "scandiweb_pass",
];