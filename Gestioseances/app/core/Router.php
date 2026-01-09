<?php

class Router
{
    private $routes = [];

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = [$controller, $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = [$controller, $method];
    }

    public function resolve(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $uri = str_replace($basePath, '', $uri);
        $uri = rtrim($uri, '/') ?: '/';

        $httpMethod = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$httpMethod][$uri])) {
            $this->dispatch($this->routes[$httpMethod][$uri]);
            return;
        }

        foreach ($this->routes[$httpMethod] ?? [] as $route => $handler) {
            $pattern = $this->convertToRegex($route);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->dispatch($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        $this->show404();
    }

    private function convertToRegex(string $route): string
    {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    private function dispatch(array $handler, array $params = []): void
    {
        [$controllerName, $method] = $handler;

        $controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die("Contrôleur non trouvé : {$controllerName}");
        }

        require_once $controllerFile;

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            die("Méthode non trouvée : {$controllerName}::{$method}");
        }

        call_user_func_array([$controller, $method], $params);
    }

    private function show404(): void
    {
        echo '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>404 - Page non trouvée</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="text-center">
                    <h1 class="display-1">404</h1>
                    <p class="lead">Page non trouvée</p>
                    <a href="' . APP_URL . '" class="btn btn-primary">Retour à l\'accueil</a>
                </div>
            </div>
        </body>
        </html>';
    }
}