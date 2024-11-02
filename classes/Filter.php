<?php 
isset($PDE) or die('Nope');

class Filter{
    public static function onlyAlphaNumeric($string){
        return preg_replace("/[^a-zA-Z0-9]+/", "", $string);
    }
    public static function normalize($str){
        return strtr(@utf8_decode($str), @utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}