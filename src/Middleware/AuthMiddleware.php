<?php
namespace Middleware;

use Core\Response;
use Models\Session;

class AuthMiddleware {
    public function handle($request, $next) {
        $token = $request->getHeader('Authorization');
        
        if (!$token) {
            return Response::json([
                'error' => 'Token no proporcionado'
            ], 401);
        }
        
        global $db;
        $session = new Session($db);
        $operatorData = $session->validate($token);
        
        if (!$operatorData) {
            return Response::json([
                'error' => 'Sesión inválida o expirada'
            ], 401);
        }
        
        $request->operator = $operatorData;
        return $next($request);
    }
}