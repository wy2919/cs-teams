<?php

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

    // robimy te metode tutaj bo bedzie wykorzystywane w każdym controllerze
    protected function render(string $templateName = null, array $variables = []) {
        $templatePath = 'public/views/'.$templateName.'.php'; // '.' lączy stringi
        $output = 'File not found';

        if (file_exists($templatePath)){
            extract($variables);

            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        print $output;
    }
}