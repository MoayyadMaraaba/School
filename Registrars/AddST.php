<?php

include "../config/db.php";

include "../config/keys.php";
include "../lib/php-jwt/JWT.php";
include "../lib/php-jwt/Key.php";

require_once("../Utils/verifyToken.php");
require_once("../Utils/helper.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get the authorization header and check if the token is valid
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        generateHttpResponse(401, "Error", "UnAuthorized", "");
        return;
    }

    $authHeader = $headers["Authorization"];
    $decoded = verifyToken($authHeader, RegistrarSecret);

    if ($decoded != null) {
        // check the request body
        if (isset($_POST["Name"]) && isset($_POST["Email"]) && isset($_POST["Age"]) && isset($_POST["Gender"]) && isset($_POST["Password"]) && isset($_POST["ClassID"]) && isset($_POST["Role"])) {
            $name = $_POST["Name"];
            $email = $_POST["Email"];
            $age = $_POST["Age"];
            $gender = $_POST["Gender"];
            $password = $_POST["Password"];
            $classID = $_POST["ClassID"];
            $role = $_POST["Role"];

            // check for empty strings
            if (!empty($name) && !empty($email) && !empty($age) && !empty($gender) && !empty($password) && !empty($role)) {

                if (strtolower($gender) == "male") {
                    $gender = 1;
                } else {
                    $gender = 0;
                }


                // Check if email is already exists
                $sql = "SELECT COUNT(*) FROM users WHERE Email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    generateHttpResponse(400, "Error", "Email already exists", "");
                    return;
                }

                // check if name already exists
                $sql = "SELECT COUNT(*) FROM users WHERE Name = :name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    generateHttpResponse(400, "Error", "Name already exists", "");
                    return;
                }

                $sql = "INSERT INTO Users (Name, Email, Age, Gender, Role, Password, ClassID) VALUES (:name, :email, :age, :gender, :role, :password, :classID)";

                $stmt = $pdo->prepare($sql);

                $hashed = md5($password);
                $classID = 0;

                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":age", $age);
                $stmt->bindParam(":gender", $gender);
                $stmt->bindParam(":role", $role);
                $stmt->bindParam(":password", $hashed);
                $stmt->bindParam(":classID", $classID);

                $stmt->execute();

                // Succesful response containing json web token
                generateHttpResponse(201, "Success", "$role Created Successfully", "");
                return;

            } else {
                // Empty inputs
                generateHttpResponse(400, "Error", "Please enter all fields", "");
            }
        } else {
            // Missing inputs
            generateHttpResponse(400, "Error", "Wrong Data", "");
        }
    }
}

?>