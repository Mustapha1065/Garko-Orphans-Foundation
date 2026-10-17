<?php

declare(strict_types=1);

namespace GOF\Helpers;

class Router
{
    /**
     * Dispatch the current request to the matching route.
     */
    public static function dispatch(array $routes): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string.
        $uri = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        /*
         * Determine the application's base path.
         *
         * Example:
         * /GOF3-light/public/index.php
         *
         * becomes:
         * /GOF3-light/public
         */
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        $basePath = str_replace('\\', '/', dirname($scriptName));

        if ($basePath === '.' || $basePath === '\\') {
            $basePath = '';
        }

        $basePath = rtrim($basePath, '/');

        /*
         * Remove the application base path.
         *
         * /GOF3-light/public/admin/idcards
         *
         * becomes:
         *
         * /admin/idcards
         */
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Normalize URI.
        $uri = '/' . ltrim($uri, '/');

        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        /*
         * Find matching route.
         */
        foreach ($routes as $route) {

            if (!isset($route[0], $route[1], $route[2])) {
                continue;
            }

            $routeMethod  = strtoupper((string) $route[0]);
            $routePattern = (string) $route[1];
            $handler      = (string) $route[2];
            $options      = $route[3] ?? [];

            /*
             * Check HTTP method.
             */
            if ($routeMethod !== 'ANY' && $routeMethod !== $method) {
                continue;
            }

            /*
             * Convert route parameters.
             *
             * Example:
             *
             * /admin/idcards/{id}
             *
             * becomes:
             *
             * /admin/idcards/(?P<id>[a-zA-Z0-9_-]+)
             */
            $pattern = preg_replace_callback(
                '/\{([a-zA-Z0-9_]+)\}/',
                function (array $match): string {
                    $name = $match[1];

                    return '(?P<' . $name . '>[a-zA-Z0-9_-]+)';
                },
                $routePattern
            );

            if ($pattern === null) {
                continue;
            }

            /*
             * Escape the route for use as a regular expression.
             */
            $pattern = '@^' . $pattern . '$@D';

            /*
             * Check URI against route.
             */
            if (preg_match($pattern, $uri, $matches) !== 1) {
                continue;
            }

            /*
             * Collect named route parameters.
             */
            $params = [];

            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $_GET[$key] = $value;
                    $params[$key] = $value;
                }
            }

            /*
             * Execute middleware.
             */
            if (isset($options['middleware'])) {
                self::executeMiddleware($options['middleware']);
            }

            /*
             * Execute controller/action.
             */
            self::executeHandler($handler, $params);

            return;
        }

        /*
         * No route matched.
         */
        http_response_code(404);

        $errorView = __DIR__ . '/../views/errors/404.php';

        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<h1>404 Not Found</h1>';
            echo '<p>The requested page could not be found.</p>';
        }
    }

    /**
     * Execute middleware.
     */
    private static function executeMiddleware($middleware): void
    {
        $middlewares = is_array($middleware)
            ? $middleware
            : [$middleware];

        foreach ($middlewares as $mw) {

            $parts = explode(':', (string) $mw, 2);

            $name  = $parts[0];
            $param = $parts[1] ?? null;

            /*
             * Convert:
             *
             * auth
             * permission:view_id_cards
             *
             * to:
             *
             * GOF\Middleware\AuthMiddleware
             * GOF\Middleware\PermissionMiddleware
             */
            $className =
                'GOF\\Middleware\\' .
                str_replace(
                    ' ',
                    '',
                    ucwords(
                        str_replace('_', ' ', $name)
                    )
                ) .
                'Middleware';

            if (!class_exists($className)) {
                continue;
            }

            $instance = new $className();

            if (!method_exists($instance, 'handle')) {
                continue;
            }

            $instance->handle($param);
        }
    }

    /**
     * Execute controller/action.
     */
    private static function executeHandler(
        string $handler,
        array $params = []
    ): void {

        $parts = explode('@', $handler, 2);

        if (count($parts) !== 2) {
            throw new \RuntimeException(
                "Invalid route handler: {$handler}"
            );
        }

        [$controllerName, $action] = $parts;

        $controllerClass =
            'GOF\\Controllers\\' .
            $controllerName;

        /*
         * Controller must exist.
         */
        if (!class_exists($controllerClass)) {
            throw new \RuntimeException(
                "Controller {$controllerClass} not found"
            );
        }

        $controller = new $controllerClass();

        /*
         * Method must exist.
         */
        if (!method_exists($controller, $action)) {
            throw new \RuntimeException(
                "Method {$action} not found in {$controllerClass}"
            );
        }

        /*
         * Pass route parameters in route order.
         */
        $controller->$action(...array_values($params));
    }
}