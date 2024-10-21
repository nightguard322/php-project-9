<?php

namespace Hexlet\Project;

use \PDO;

final class Connector
{
    private static $conn = null;
    public static $test = false;

    private function __construct()
    {

    }

    public function connect()
    {
        $params = parse_url($_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL'));
        $params['dbname'] = ltrim($params['path'], '/');
        $conStrPg = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'],
            $params['port'],
            $params['dbname'],
            $params['user'],
            $params['pass']
        );
        $conStrTest = 'sqlite:database.sqlite';
        $conStr = $this->test ? $conStrTest : $conStrPg;
        
        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $pdo;
    }

    public static function get()
    {
        if (null === static::$conn) {
            static::$conn = new self();
        }
        return static::$conn;
    }
}
