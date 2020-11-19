<?php

class AppController {

    // robimy te metode tutaj bo bedzie wykorzystywane w każdym controllerze
    protected function render(string $templateName = null) {
        $templatePath = 'public/views/'.$templateName.'.html'; // '.' lączy stringi
        $output = 'File not found';

        if (file_exists($templatePath)){
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }

        print $output;
    }
}