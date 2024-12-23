<?php
namespace Core;

class Router {
    private $routes = [];
    
    public function addRoute($method, $path, $handler, $middleware = []) {
        $path = '/' . trim($path, '/');
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch($request) {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                try {
                    if (!empty($route['middleware'])) {
                        foreach ($route['middleware'] as $middlewareClass) {
                            $middleware = new $middlewareClass();
                            $response = $middleware->handle($request, function($request) use ($route) {
                                return $this->executeHandler($route['handler'], $request);
                            });
                            if ($response) {
                                return $response;
                            }
                        }
                    }
                    
                    return $this->executeHandler($route['handler'], $request);
                } catch (\Exception $e) {
                    return Response::json([
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }
        
        return Response::json([
            'error' => 'Not Found'
        ], 404);
    }
    
    private function executeHandler($handler, $request) {
        if (is_array($handler)) {
            list($controllerClass, $method) = $handler;
            $controller = new $controllerClass();
            return $controller->$method($request);
        }
        
        return call_user_func($handler, $request);
    }
    
    private function matchPath($routePath, $requestPath) {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }
}