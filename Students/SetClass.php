<?php

include "../config/keys.php";
include "../config/db.php";

require_once("../Utils/verifyToken.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get the authorization header and check if the token is valid
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        generateHttpResponse(401, "Error", "UnAuthorized", "");
        return;
    }

    $authHeader = $headers["Authorization"];
    $decoded = verifyToken($authHeader, RegistrarSecret);

    if ($decoded != null) {
        // Get the data from the body
        if (isset($_POST["ClassID"]) && isset($_POST["StudentID"])) {
            $classID = $_POST["ClassID"];
            $studentID = $_POST["StudentID"];

            if (!empty($classID) && !empty($studentID)) {
                $sql = "UPDATE Users SET ClassID = :classID WHERE ID = :id";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":classID", $classID);
                $stmt->bindParam(":id", $studentID);

                $stmt->execute();

                generateHttpResponse(200, "Success", "Student Class Updated", "");
                return;
            } else {
                generateHttpResponse(400, "Error", "Please enter all fields", "");
            }
        } else {
            // Missing inputs
            generateHttpResponse(400, "Error", "Wrong Data", "");
        }
    }
}

?>