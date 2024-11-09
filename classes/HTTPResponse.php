<?php 
isset($PDE) or die('Nope');

class HTTPResponse {
    public static function forbidden($message){
        global $response_json;
        header('HTTP/1.1 403 Forbidden');
        if ($response_json){
            exit(json_encode(array("error"=>'403', 'message'=>$message)));
        } 
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
