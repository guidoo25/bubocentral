<?php
namespace Controllers;

use Core\App;
use Core\Request;
use Core\Response;
use Models\Operator;

class OperatorController
{
    private $db;
    private $operatorModel;

    public function __construct()
    {
        $this->db = App::getInstance()->getDB();
        $this->operatorModel = new Operator($this->db);
    }

    public function create(Request $request)
    {
        $data = $request->getJson();

        // Validate input
        if (!isset($data['telefono']) || !isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
            return Response::json([
                'error' => 'Faltan campos requeridos'
            ], 400);
        }

        // Validate phone number (simple validation, you might want to use a library for more complex validation)
        if (!preg_match("/^[0-9]{9,15}$/", $data['telefono'])) {
            return Response::json([
                'error' => 'Número de teléfono inválido'
            ], 400);
        }

        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return Response::json([
                'error' => 'Email inválido'
            ], 400);
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Prepare operator data
        $operatorData = [
            'telefono' => $data['telefono'],
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'password_hash' => $hashedPassword,
            'estado' => 'OFF' // Default state
        ];

        try {
            $newOperatorId = $this->operatorModel->create($operatorData);

            if ($newOperatorId) {
                return Response::json([
                    'message' => 'Operador creado exitosamente',
                    'id' => $newOperatorId
                ], 201);
            } else {
                return Response::json([
                    'error' => 'No se pudo crear el operador'
                ], 500);
            }
        } catch (\PDOException $e) {
            // Check for duplicate entry error
            if ($e->getCode() == '23000') {
                return Response::json([
                    'error' => 'El teléfono o email ya está en uso'
                ], 409);
            }

            return Response::json([
                'error' => 'Error en la base de datos'
            ], 500);
        }
    }
}

