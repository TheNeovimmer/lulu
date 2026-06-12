<?php
namespace App\Core;

class View {
    public static function render($view, $data = [], $layout = 'front') {
        extract($data);
        ob_start();
        require __DIR__ . "/../../views/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/layouts/{$layout}.php";
    }

    public static function renderPartial($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../views/{$view}.php";
    }
}
