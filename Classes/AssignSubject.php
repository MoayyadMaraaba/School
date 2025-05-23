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
        if (isset($_POST["ClassID"]) && isset($_POST["SubjectID"])) {
            $subjectID = $_POST["SubjectID"];
            $classID = $_POST["ClassID"];

            if (!empty($classID) && !empty($subjectID)) {

                $sql = "INSERT INTO ClassSubjects (SubjectID, ClassID) VALUES (:subjectID, :classID)";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":subjectID", $subjectID);
                $stmt->bindParam(":classID", $classID);

                $stmt->execute();

                generateHttpResponse(201, "Success", "Subject Assigned Successfully", "");
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