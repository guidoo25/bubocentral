<?php
namespace Controllers;

use Models\Operator;
use Models\Session;
use Core\Response;

class AuthController {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function login($request) {
        $data = $request->getJson();
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return Response::json([
                'error' => 'Email y contraseña son requeridos'
            ], 400);
        }
        
        $operator = new Operator($this->db);
        $operatorData = $operator->authenticate($data['email'], $data['password']);
        
        if (!$operatorData) {
            return Response::json([
                'error' => 'Credenciales inválidas'
            ], 401);
        }
        
        $session = new Session($this->db);
        $token = $session->create($operatorData['id']);
        
        return Response::json([
            'token' => $token,
            'operator' => $operatorData
        ]);
    }
    
    public function logout($request) {
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return Response::json([
                'error' => 'Token no proporcionado'
            ], 401);
        }
        
        $session = new Session($this->db);
        $session->invalidate($token);
        
        return Response::json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }
}