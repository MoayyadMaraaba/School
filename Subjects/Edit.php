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
        if (isset($_POST["ID"]) && isset($_POST["Name"]) && isset($_POST["TeacherID"]) && isset($_POST["Time"]) && isset($_POST["Start"]) && isset($_POST["Day"])) {
            $id = $_POST["ID"];
            $name = $_POST["Name"];
            $teacherID = $_POST["TeacherID"];
            $time = $_POST["Time"];
            $start = $_POST["Start"];
            $day = $_POST["Day"];

            if (!empty($id) && !empty($name) && !empty($teacherID) && !empty($time) && !empty($start) && !empty($day)) {

                $sql = "UPDATE Subjects SET Name = :name, TeacherID = :teacherID, Time = :time, Start = :start, Day = :day WHERE ID = :id";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":teacherID", $teacherID);
                $stmt->bindParam(":time", $time);
                $stmt->bindParam(":start", $start);
                $stmt->bindParam(":day", $day);

                if ($stmt->execute()) {
                    generateHttpResponse(200, "Success", "Subject Updated", "");
                    return;
                } else {
                    generateHttpResponse(404, "Error", "Wrong ID", "");
                }
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