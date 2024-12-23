<?php
use Controllers\AuthController;
use Controllers\OperatorController;
use Middleware\AuthMiddleware;

// Definir las rutas de autenticación
$app->router->addRoute('POST', '/api/auth/login', [AuthController::class, 'login']);
$app->router->addRoute('POST', '/api/auth/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
$app->router->addRoute('POST', '/api/operators', [OperatorController::class, 'create']);

// Definir las rutas de operadores
// $app->router->addRoute('GET', '/api/operators/me', [OperatorController::class, 'getProfile'], [AuthMiddleware::class]);
// $app->router->addRoute('PUT', '/api/operators/me/status', [OperatorController::class, 'updateStatus'], [AuthMiddleware::class]);