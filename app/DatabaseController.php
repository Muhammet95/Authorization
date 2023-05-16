<?php

namespace App;

use DevCoder\DotEnv;
use PDO;

class DatabaseController
{
    protected PDO $connection;
    public function __construct()
    {
        $absolutePathToEnvFile = __DIR__ . '/../.env';
        (new DotEnv($absolutePathToEnvFile))->load();
        $servername = getenv('DATABASE_HOST');
        $databasename = getenv('DATABASE_NAME');
        $username = getenv('DATABASE_USER');
        $password = getenv('DATABASE_PASSWORD');

        $this->connection = new PDO("mysql:host=$servername;dbname=$databasename;charset=utf8", $username, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->query("SET wait_timeout=28800;");
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}