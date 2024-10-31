<?php

namespace Hexlet\Project;

use Carbon\Carbon;
use GuzzleHttp;
use DiDom\Document;

/**
 * Make url checks
 */
class Checker
{
    /**
     * [Description for $conn]
     *
     * @var \PDO
     */
    private \PDO $_conn;
    /**
     * [Description for $client]
     *
     * @var GuzzleHttp\Client
     */
    private GuzzleHttp\Client $_client;

    /**
     * [Description for __construct]
     *
     * @param \PDO $conn
     * 
     */
    public function __construct(\PDO $conn)
    {
        $this->_conn = $conn;
        $this->_client = new GuzzleHttp\Client();
    }

    /**
     * @param  int $id
     * @param  bool $onlyLast
     * @return array<mixed>
     */
    public function getChecks(int $id, bool $onlyLast = false): array
    {
        $stmt = $this->_conn->prepare('SELECT * from url_checks WHERE url_id = :id');
        $stmt->execute([$id]);
        $checks = [];
        while ($row = $stmt->fetch()) {
            $checks[] = $row;
        }
        /** @var array<int, string> $last */
        $last = end($checks) ?: [];
        return $onlyLast ? $last : $checks;
    }

    /**
     * @param  int $id
     * @param  string $url
     * @return array<string>|false
     */
    public function makeCheck(int $id, string $url): array|false
    {
        $messages = [
            'success' => 
                ['success','Страница успешно проверена'],
            'warning' => 
                ['warning', 'Проверка была выполнена успешно, но сервер ответил с ошибкой'],
            'danger' => 
                ['danger','Произошла ошибка при проверке, не удалось подключиться']
        ];
        $message = $messages['success'];
        try {
            $response = $this->_client->request('GET', $url);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                /** @var \Psr\Http\Message\ResponseInterface $response */
                $response = $e->getResponse();
                $body = (string)$response->getBody();
                if (empty($body)) {
                    return false;
                }
                $message = $messages['warning'];
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return $messages['danger'];
        }
            $document = isset($body) ? new Document($body) : new Document($url, true);
            /** @var \DiDom\Element $h1 */
            $h1 = optional($document->first('h1'));
            /** @var \DiDom\Element $title */
            $title = optional($document->first('title'));
            /** @var \DiDom\Element $description */
            $description = optional($document->first('description'));
            $urlData = [
                'url_id' => $id,
                'status_code' => $response->getStatusCode(),
                'h1' => $h1->text(),
                'title' => $title->text(),
                'description' => $description->attr('content'),
                'created_at' => Carbon::now()->toDateTimeString()
            ];
            $stmt = $this->_conn->prepare(
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
        return $message;
    }
}
