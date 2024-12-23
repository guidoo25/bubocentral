<?php
namespace Core;

use Core\Database as CoreDatabase;
use Database;

class App {
    private static $instance = null;
    public $router;
    public $db;
    
    public function __construct() {
        self::$instance = $this;
        $this->router = new Router();
        $this->db = new CoreDatabase(); // Ahora usa el namespace correcto
        
        // Establecer el manejador de errores
        $this->setupErrorHandler();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function setupErrorHandler() {
        set_error_handler(function($severity, $message, $file, $line) {
            if (error_reporting() & $severity) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }
        });
        
        set_exception_handler(function($e) {
            $response = [
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ];
            
            if (getenv('APP_DEBUG') === 'true') {
                $response['file'] = $e->getFile();
                $response['line'] = $e->getLine();
                $response['trace'] = $e->getTrace();
            }
            
            Response::json($response, $response['code'])->send();
        });
    }
    
    public function run() {
        try {
            // Crear una nueva instancia de Request
            $request = new Request();
            
            // Despachar la solicitud a través del router
            $response = $this->router->dispatch($request);
            
            // Enviar la respuesta
            if ($response instanceof Response) {
                $response->send();
            } else {
                echo $response;
            }
        } catch (\Throwable $e) {
            // Manejar cualquier error no capturado
            $response = [
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ];
            
            if (getenv('APP_DEBUG') === 'true') {
                $response['file'] = $e->getFile();
                $response['line'] = $e->getLine();
                $response['trace'] = $e->getTrace();
            }
            
            Response::json($response, $response['code'])->send();
        }
    }
    
    public function getDB() {
        return $this->db;
    }
}