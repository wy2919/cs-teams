<?php

require_once __DIR__ . '/../security/RouteGuard.php';
class AppController {

    private $request;

    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];
    }

    protected function isGet(): bool {
        return $this->request === "GET";
    }

    protected function isPost(): bool {
        return $this->request === 'POST';
    }

    protected function render(string $templateName = null, array $variables = []) {
        $templatePath = 'public/views/'.$templateName.'.php';
        $output = 'File not found';

        if (file_exists($templatePath)){
            extract($variables);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        print $output;
        return null;
    }

    protected function handleException($exception) {
        return $this->render('error', ['message' => $exception->getMessage()]);
    }

    protected function decodeJsonRequest() {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
            header('Content-type: application/json');
            http_response_code(200);
            return $decoded;
        }
        $this->render('error', ['message' => 'Request not supported.']);
        return false;
    }
}