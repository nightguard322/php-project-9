<?php

namespace Hexlet\Project;

use Carbon\Carbon;
use GuzzleHttp;

class Checker
{
    private \PDO $conn;
    private $client;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
        $this->client = new GuzzleHttp\Client();
    }

    public function getChecks(int $id): ?array
    {
        $checks = [];
        $stmt = $this->conn->prepare('SELECT * from url_checks WHERE url_id = :id');
        $stmt->execute([$id]);
        while ($row = $stmt->fetch()) {
            $checks[] = $row;
        }
        return $checks;
    }

    public function makeCheck(int $id, string $url): bool
    {
        $response = $this->client->request('GET', $url);
        $urlData = [
            'url_id' => $id,
            'status_code' => $response->getStatusCode(),
            'h1' => '',
            'title' => '',
            'description' => '',
            'created_at' => Carbon::now()->toDateString()
        ];
        $stmt = $this->conn->prepare(
            "INSERT INTO url_checks
            (url_id, status_code, h1, title, description, created_at)
            VALUES
            (:url_id, :status_code, :h1, :title, :description, :created_at)"
        );
        $stmt->execute([
                ':url_id' => $urlData['url_id'],
                ':status_code' => $urlData['status_code'],
                ':h1' => $urlData['h1'],
                ':title' => $urlData['title'],
                ':description' => $urlData['description'],
                ':created_at' => $urlData['created_at']
        ]);
        $stmt->execute([$id]);
        return true;
    }
}