<?php

if (!function_exists('hash_string')) {
    /**
     * Create a short, fairly unique, urlsafe hash for the input string.
     */
    function hash_string( $input, $length = 4 ){

        $input= $input.time();
        $hash_base64 = base64_encode( hash( 'sha256', $input, true ) );
        $hash_urlsafe = strtr( $hash_base64, '+/', '-_' );
        $hash_urlsafe = rtrim( $hash_urlsafe, '=' );
        return substr( $hash_urlsafe, 0, $length );
    }
}

if (!function_exists('redisSort')) {
    /**
     * Create a short, fairly unique, urlsafe hash for the input string.
     */
    function redisSort($a, $b) {
        return $a['Click'] < $b['Click'];
    }
}
