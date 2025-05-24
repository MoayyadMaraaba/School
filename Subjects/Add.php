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
        if (isset($_POST["Name"]) && isset($_POST["TeacherID"]) && isset($_POST["End"]) && isset($_POST["Start"]) && isset($_POST["Day"])) {
            $name = $_POST["Name"];
            $teacherID = $_POST["TeacherID"];
            $end = $_POST["End"];
            $start = $_POST["Start"];
            $day = $_POST["Day"];

            if (!empty($name) && !empty($teacherID) && !empty($end) && !empty($start) && !empty($day)) {

                // Check if the tacher is availabe in this time or not
                $sql = "SELECT Start, End FROM `subjects` WHERE (Start < :newEnd) AND (End > :newStart) AND Day = :day AND TeacherID = :teacherID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":newEnd", $end);
                $stmt->bindParam(":newStart", $start);
                $stmt->bindParam(":day", $day);
                $stmt->bindParam(":teacherID", $teacherID);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    generateHttpResponse(400, "Error", "Time is not available", "");
                    return;
                }


                // add subject
                $sql = "INSERT INTO Subjects (Name, TeacherID, Start, End, Day) VALUES (:name, :teacherID, :start, :end, :day)";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":teacherID", $teacherID);
                $stmt->bindParam(":start", $start);
                $stmt->bindParam(":end", $end);
                $stmt->bindParam(":day", $day);

                $stmt->execute();

                generateHttpResponse(201, "Success", "Subject Created", "");
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