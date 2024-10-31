<?php

namespace Hexlet\Project;

final class Connector
{
    /**
     * @var Connector|null|null
     */
    private static ?Connector $conn = null;
    public static bool $test = false;

    private function __construct()
    {
    }

    public function connect(): \PDO
    {
        /**
     * @var array{
     *  dbname: string,
     *  path: string,
     *  host: string,
     *  port: int,
     *  user: string,
     *  pass: string
     * } $params
     */
        $params = parse_url($_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL'));
        $params['dbname'] = ltrim($params['path'], '/');
        $params['port'] = 5432;
        $conStrPg = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $params['host'],
            $params['port'],
            $params['dbname'],
            $params['user'],
            $params['pass']
        );
        $conStrTest = 'sqlite:database.sqlite';
        $conStr = self::$test ? $conStrTest : $conStrPg;

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $pdo;
    }

    /**
     * @return self
     */
    public static function get(): self
    {
        if (null === static::$conn) {
            static::$conn = new self();
        }
        return static::$conn;
    }
}
