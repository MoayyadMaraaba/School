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
                $sql = "SELECT Day, Start, End FROM subjects WHERE ID = :subjectID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":subjectID", $subjectID);

                $stmt->execute();

                $subject = $stmt->fetchAll()[0];

                // Check if the tacher is availabe in this time or not
                $sql = "SELECT Start, End FROM `subjects` WHERE (Start < :newEnd) AND (End > :newStart) AND Day = :day AND TeacherID = :teacherID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":newEnd", $subject["End"]);
                $stmt->bindParam(":newStart", $subject["Start"]);
                $stmt->bindParam(":day", $subject["Day"]);
                $stmt->bindParam(":teacherID", $teacherID);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    generateHttpResponse(400, "Error", "Teacher is busy", "");
                    return;
                }

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