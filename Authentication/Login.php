<?php

use Firebase\JWT\JWT;

include "../config/db.php";

include "../config/keys.php";
include "../lib/php-jwt/JWT.php";
include "../lib/php-jwt/Key.php";

require_once("../Utils/helper.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // check if theres is a key for email and password
    if (isset($_POST["Email"]) && isset($_POST["Password"])) {

        // Get email and email from user
        $email = $_POST["Email"];
        $password = $_POST["Password"];

        // check for empty strings
        if (!empty($email) && !empty($password)) {

            $sql = "Select ID,Role From users where Email=:email AND Password=:password";

            $stmt = $pdo->prepare($sql);

            $hashed = md5($password);

            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed);

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                // Get user id and role 
                $userId = $results[0]["ID"];
                $role = $results[0]["Role"];

                // generating json web token
                $key = getKeyBasedOnRole($role);
                $payload = ["id" => $userId, "role" => $role, "exp" => time() + (3600 * 48)];
                $jwt = JWT::encode($payload, $key, "HS256");

                // Succesful response containing json web token
                generateHttpResponse(200, "Success", "User Login Successfully", ["Token" => $jwt, "Role" => $role]);
                return;
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

?>