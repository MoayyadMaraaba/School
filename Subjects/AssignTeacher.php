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
        if (isset($_POST["TeacherID"]) && isset($_POST["SubjectID"])) {
            $teacherID = $_POST["TeacherID"];
            $subjectID = $_POST["SubjectID"];

            if (!empty($teacherID) && !empty($subjectID)) {

                $sql = "UPDATE Subjects SET TeacherID = :teacherID WHERE ID = :subjectID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":teacherID", $teacherID);
                $stmt->bindParam(":subjectID", $subjectID);

                $stmt->execute();

                generateHttpResponse(200, "Success", "Teacher Assigned Successfully", "");
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