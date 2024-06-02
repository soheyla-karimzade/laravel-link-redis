<?php

if (!function_exists('hash_string')) {
    /**
     * Create a short, fairly unique, urlsafe hash for the input string.
     */
    function hash_string( $input, $length = 4 ){

        $input= $input.time();
        // Create a raw binary sha256 hash and base64 encode it.
        $hash_base64 = base64_encode( hash( 'sha256', $input, true ) );
        // Replace non-urlsafe chars to make the string urlsafe.
        $hash_urlsafe = strtr( $hash_base64, '+/', '-_' );
        // Trim base64 padding characters from the end.
        $hash_urlsafe = rtrim( $hash_urlsafe, '=' );
        // Shorten the string before returning.
        return substr( $hash_urlsafe, 0, $length );
    }

    // Define a custom comparison function
    function redisSort($a, $b) {
//        echo $a['Click'];
//        echo $b['Click'];
//        echo   $a['Click'] > $b['Click'];
//        die;
        return $a['Click'] < $b['Click']; // Compare ages
    }
}
