<?php

namespace DLPL\Prelaunch;

class Tools
{
    /**
     * Comprueba si un string empieza por otro
     * @param mixed $haystack
     * @param mixed $needle
     * @return bool
     */
    public static function strStartsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }

    /**
     * Comprueba si un allamada es ajax
     * @return bool
     */
    public static function is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'));
    }

    public static function isSerialize($string) {
        
        if (!is_string($string) || strlen($string) < 4) {
            return false;
        }
    
        $string = trim($string);
    
        if (preg_match('/^(a|O|s|i|d|b):/', $string)) {
            $data = @unserialize($string);
            return $data !== false || $string === serialize(false);
        }
    
        return false;
    }

}
