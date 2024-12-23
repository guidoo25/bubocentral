<?php
namespace Core;

class Response {
    private $status = 200;
    private $headers = [];
    private $body;

    public function setStatus($code) {
        $this->status = $code;
        return $this;
    }

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public static function json($data, $status = 200) {
        $response = new self();
        $response->setHeader('Content-Type', 'application/json');
        $response->setStatus($status);
        $response->setBody(json_encode($data));
        return $response;
    }

    public function send() {
        // Establecer código de estado
        http_response_code($this->status);

        // Establecer headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Enviar el cuerpo de la respuesta
        echo $this->body;
        exit;
    }
}