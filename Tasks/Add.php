<?php

include "../config/keys.php";
include "../config/db.php";

require_once("../Utils/verifyToken.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get the authorization header and check if the token is valid
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        generateHttpResponse(401, "Error", "Unauthorized", "");
        return;
    }

    $authHeader = $headers["Authorization"];
    $decoded = verifyToken($authHeader, TeacherSecret);

    if ($decoded != null) {
        // Get the data from the body
        if (isset($_POST["Type"]) && isset($_POST["Description"]) && isset($_POST["ClassID"])) {
            $name = $_POST["Name"];

            if (!empty($name)) {

                $sql = "INSERT INTO Tasks (Name) VALUES (:name)";

                $stmt = $pdo->prepare($sql);


                $stmt->execute();

                generateHttpResponse(201, "Success", "Task Created", "");
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