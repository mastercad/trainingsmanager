<?php
    $controllerName = 'test-ich-jetzt-na-dann';
    echo ucFirst(preg_replace_callback('/(\-[a-z]{1})/', "upperCase", $controllerName));

    function upperCase(array $piece) {
        return ucfirst(str_replace('-', '', $piece[1]));
    }