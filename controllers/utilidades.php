<?php
/**
 * Recibe una url
 */
class Utilidades
{
    public static function parseUriParameters($uri)
    {
        $parts = explode('?', $uri);


        if (count($parts) == 1) {
            return [];
        }

        $paramString = $parts[1];

        $paramPairs = explode('&', $paramString);

        $param = [];

        foreach ($paramPairs as $pair) {
            $item = explode('=', $pair);
            if (count($item) == 2) {
                $key = urldecode($item[0]);
                $value = urldecode($item[1]);
                $param[$key] = $value;
            }

        }

        return $param;

    }
    public static function getParameterValue(array $params, string $paramName){
        if (isset($params[$paramName])) {
            return $params[$paramName];
        }else{
            return null;
        }

        // return $params[$paramName] ?? null;
    
    }

}