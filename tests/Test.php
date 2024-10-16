<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class Test extends TestCase
{
    private $client;
    private $conn;

    public function setUp(): void
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'cookies' => true
        ]);
        
        $this->conn = new \PDO('sqlite:public/database.sqlite');
        $pathInitDbSql = implode('/', [dirname(__DIR__), 'tests', 'initdb.sql']);
        $initSql = file_get_contents($pathInitDbSql);
        $this->conn->exec($initSql);
    }

    public function testRoot()
    {
        $response = $this->client->get('/');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUrls()
    {
        $response = $this->client->get('/urls');
        $body = $response->getBody()->getContents();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('google.com', $body);
        $this->assertStringContainsString('2024-10-14 12:00:00', $body);
    }

    public function testShow()
    {
        $response = $this->client->get('/urls/1');
        $body = $response->getBody()->getContents();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('google.com', $body);
        $this->assertStringContainsString('2024-10-14 10:00:00', $body);
    }

    public function testAddNewUrl()
    {
        $urlParams = ['url' => ['name' => 'http://yandex.ru']];
        $response = $this->client->post('/urls', [
            'form_params' => $urlParams,
            'http_errors' => false
        ]);
        $response = $this->client->get('/urls');
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('http://yandex.ru', $body);
    }

    public function testAddSameUrl()
    {
        $urlParams = ['url' => ['name' => 'http://yandex.ru']];
        $response = $this->client->post('/urls', [
            'form_params' => $urlParams,
            'http_errors' => false
        ]);
        $id = $this->conn->lastInsertId();
        $response = $this->client->post('/urls', [
            'form_params' => $urlParams,
            'http_errors' => false
        ]);
        $newId = $this->conn->lastInsertId();
        $response = $this->client->get("/urls/$id");
        $body = $response->getBody()->getContents();
        $this->assertStringContainsString('Страница уже существует', $body);
        $this->assertEquals($id, $newId);
    }
}