<?php
namespace App\Core;

abstract class Controller
{
    /**
     * Render a view inside a layout.
     *
     * @param string $view   Dot path relative to app/Views, e.g. "site.home"
     * @param array  $data   Variables extracted into the view scope
     * @param string $layout Dot path of the layout, e.g. "layouts.site"
     */
    protected function view(string $view, array $data = [], string $layout = 'layouts.site'): void
    {
        $content = $this->render($view, $data);
        echo $this->render($layout, array_merge($data, ['content' => $content]));
    }

    /** Render a view and return the markup instead of echoing it. */
    protected function render(string $view, array $data = []): string
    {
        $file = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException("View not found: {$view} ({$file})");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function back(string $fallback = '/'): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host    = $_SERVER['HTTP_HOST'] ?? '';

        // Only follow a referer that points back at this host.
        if ($referer !== '' && parse_url($referer, PHP_URL_HOST) === $host) {
            header('Location: ' . $referer);
            exit;
        }

        $this->redirect($fallback);
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function notFound(string $message = 'The page you were looking for could not be found.'): void
    {
        http_response_code(404);
        $this->view('site.error', [
            'pageTitle' => 'Not Found',
            'code'      => 404,
            'message'   => $message,
        ]);
        exit;
    }

    /** True when the request is an XHR/fetch call expecting JSON. */
    protected function wantsJson(): bool
    {
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $accept        = $_SERVER['HTTP_ACCEPT'] ?? '';

        return strtolower($requestedWith) === 'xmlhttprequest'
            || str_contains($accept, 'application/json');
    }
}
