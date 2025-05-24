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
    $decoded = verifyToken($authHeader, StudentSecret);

    if ($decoded != null) {
        // Get the data from the body
        if (isset($_POST["Answer"])) {
            $answer = $_POST["Answer"];

            if (!empty($answer)) {

                $sql = "UPDATE Tasks SET Answer = :answer WHERE UserID = :userID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":answer", $answer);
                $stmt->bindParam(":userID", $decoded->id);
                $stmt->execute();

                generateHttpResponse(200, "Success", "Task Submission", "");
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