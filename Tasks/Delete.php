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
        if (isset($_POST["Name"]) && isset($_POST["SubjectID"])) {
            $name = $_POST["Name"];
            $subjectID = $_POST["SubjectID"];

            if (!empty($name) && !empty($subjectID)) {

                $sql = "DELETE FROM Tasks WHERE Name = :name AND SubjectID = :subjectID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":subjectID", $subjectID);
                $stmt->execute();

                generateHttpResponse(200, "Success", "Task Deleted", "");
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