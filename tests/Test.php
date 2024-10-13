<?php

namespace Hexlet\Project;

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'cookies' => true
        ]);
        
        $conn = new \PDO('sqlite:public/database.sqlite');


    }



}