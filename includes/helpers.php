<?php

/*
 * getallheaders function doesn't exist bug fix
 * @author s3bubble
 * @params none
 */
if (!function_exists('getallheaders')) {

    function getallheaders() {
    
        $headers = [];
    
        foreach ($_SERVER as $name => $value) {
    
            if (substr($name, 0, 5) == 'HTTP_') {
    
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
    
            }
    
        }
    
        return $headers;
    
    }

}