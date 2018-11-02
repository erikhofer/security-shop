<?php

class DatabaseConnection extends PDO
{
    private static $instance;

    /**
     * @return DatabaseConnection
     */
    public static function getInstance()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        /** @var array $config */
        $config = include __DIR__ . '/../config/config.php';

        $username = $config['db']['user'];
        $password = $config['db']['password'];
        $host = $config['db']['host'];
        $database = $config['db']['database'];

        self::$instance = new static('mysql:dbname=' . $database . ';host=' . $host, $username, $password);

        return self::$instance;
    }
}
