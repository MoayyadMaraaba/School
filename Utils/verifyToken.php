<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWTExceptionWithPayloadInterface;

require_once '../lib/php-jwt/JWTExceptionWithPayloadInterface.php';
require_once '../lib/php-jwt/JWT.php';
require_once '../lib/php-jwt/Key.php';
require_once '../lib/php-jwt/ExpiredException.php';
require_once '../lib/php-jwt/SignatureInvalidException.php';
require_once '../lib/php-jwt/BeforeValidException.php';
require_once 'helper.php';

function verifyToken($token, $key)
{
    if ($token && str_starts_with($token, 'Bearer ')) {
        $token = substr($token, 7);
    }

    try {
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        generateHttpResponse(401, "Error", "Unauthorized", "");
        return;
    }
}

function isAuthorized($keys, $token)
{
    if ($token && str_starts_with($token, 'Bearer ')) {
        $token = substr($token, 7);
    }

    $counter = 0;
    $decode = null;

    for ($i = 0; $i < count($keys); $i++) {
        try {
            $decode = JWT::decode($token, new Key($keys[$i], 'HS256'));
        } catch (Exception $e) {
            $counter++;
        }

    }

    if ($counter == count($keys)) {
        generateHttpResponse(401, "Error", "Unauthorized", "");
        return;
    }

    return $decode;
}

?>