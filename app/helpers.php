<?php declare(strict_types=1);

if (! function_exists('get_ip')) {
    function get_ip()
    {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $ip_array = explode(",", $ip);
            return $ip_array[0];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}