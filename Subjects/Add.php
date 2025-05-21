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
        if (isset($_POST["Name"]) && isset($_POST["TeacherID"]) && isset($_POST["Time"]) && isset($_POST["Start"]) && isset($_POST["Day"])) {
            $name = $_POST["Name"];
            $teacherID = $_POST["TeacherID"];
            $time = $_POST["Time"];
            $start = $_POST["Start"];
            $day = $_POST["Day"];

            if (!empty($name) && !empty($teacherID) && !empty($time) && !empty($start) && !empty($day)) {

                $sql = "INSERT INTO Subjects (Name, TeacherID, Time, Start, Day) VALUES (:name, :teacherID, :time, :start, :day)";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":teacherID", $teacherID);
                $stmt->bindParam(":time", $time);
                $stmt->bindParam(":start", $start);
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