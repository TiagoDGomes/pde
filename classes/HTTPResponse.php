<?php 
isset($PDE) or die('Nope');

class HTTPResponse {
    public static function forbidden($message){
        header('HTTP/1.1 403 Forbidden');
        exit($message);
    }

    public static function redirect($url){        
        header("Location: $url");
        exit();
    }

    public static function JSON($arr){
        header("Content-Type: application/json");
        exit(json_encode($arr));
    }
}
