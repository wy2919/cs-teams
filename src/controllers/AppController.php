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

    protected function validateModelExists($model, $onErrorMessage) : bool{
        if(!isset($model)) {
            $this->render('error', ['message'=>$onErrorMessage]);
            return false;
        }
        return true;
    }


    protected function handleException($message) {
        return $this->render('error', ['message'=>$message]);
    }
}