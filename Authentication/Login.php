<?php

use Firebase\JWT\JWT;

include "../config/db.php";

include "../config/keys.php";
include "../lib/php-jwt/JWT.php";
include "../lib/php-jwt/Key.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // check if theres is a key for name and password
    if (isset($_POST["name"]) && isset($_POST["password"])) {

        // Get name and email from user
        $name = $_POST["name"];
        $password = $_POST["password"];

        // check for empty strings
        if (!empty($name) && !empty($password)) {

            $sql = "Select ID,Role From users where Name=:name AND Password=:password";

            $stmt = $pdo->prepare($sql);

            $hashed = md5($password);

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":password", $hashed);

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);



            if (count($results) > 0) {
                // Get user id and role 
                $userId = $results[0]["ID"];
                $role = $results[0]["Role"];

                // generating json web token
                $key = getKeyBasedOnRole($role);
                $payload = ["id" => $userId, "role" => $role, "exp" => time() + 3600];
                $jwt = JWT::encode($payload, $key, "HS256");


                // Succesful response containing json web token
                generateHttpResponse(200, "Success", "User Login Successfully", ["Token" => $jwt]);
            } else {
                // Wrong Credentials
                generateHttpResponse(400, "Error", "Wrong Credentials", "");
            }
        } else {
            // Empty inputs
            generateHttpResponse(400, "Error", "Please enter all fields", "");
        }
    } else {
        // Missing inputs
        generateHttpResponse(400, "Error", "Wrong Data", "");
    }
}

function getKeyBasedOnRole($role)
{
    $key = "";
    if ($role == "Teacher") {
        $key = TeacherSecret;
    } else if ($role == "Student") {
        $key = StudentSecret;
    } else if ($role == "Registrar") {
        $key = RegistrarSecret;
    }
    return $key;
}

function generateHttpResponse($code, $status, $message, $data = [])
{
    http_response_code($code);
    $response = [
        "Status" => $status,
        "Message" => $message,
    ];

    if (!empty($data)) {
        $response = array_merge($response, $data);
    }

    echo json_encode($response);
}
?>