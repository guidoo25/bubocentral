<?php
namespace Core;

class Request {
    private $method;
    private $uri;
    private $path;
    private $headers;
    private $params;
    private $body;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->path = $this->parsePath();
        $this->headers = $this->parseHeaders();
        $this->params = $this->parseParams();
        $this->body = $this->parseBody();
    }

    private function parsePath() {
        $path = parse_url($this->uri, PHP_URL_PATH);
        $path = preg_replace('#^/bubocentral/public#', '', $path);
        return '/' . trim($path, '/');
    }

    private function parseHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    private function parseParams() {
        $params = [];
        parse_str($_SERVER['QUERY_STRING'] ?? '', $params);
        return $params;
    }

    private function parseBody() {
        $body = file_get_contents('php://input');
        $contentType = $this->getHeader('Content-Type');

        if ($contentType !== null) {
            if (strpos($contentType, 'application/json') !== false) {
                return json_decode($body, true);
            }

            if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                parse_str($body, $data);
                return $data;
            }
        }

        return $body;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getPath() {
        return $this->path;
    }

    public function getHeader($name) {
        return $this->headers[$name] ?? null;
    }

    public function getParam($name) {
        return $this->params[$name] ?? null;
    }

    public function getJson() {
        return $this->body;
    }

    public function getBody() {
        return $this->body;
    }

    public function all() {
        return array_merge($this->params, is_array($this->body) ? $this->body : []);
    }

    public function isMethod($method) {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function isAjax() {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    public function expectsJson() {
        $accept = $this->getHeader('Accept');
        return $this->isAjax() || ($accept !== null && strpos($accept, 'application/json') !== false);
    }
}