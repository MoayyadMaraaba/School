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
        if (isset($_POST["Name"]) && isset($_POST["Type"]) && isset($_POST["Description"]) && isset($_POST["SubjectID"])) {
            $name = $_POST["Name"];
            $type = $_POST["Type"];
            $description = $_POST["Description"];
            $subjectID = $_POST["SubjectID"];

            if (!empty($name) && !empty($type) && !empty($description) && !empty($subjectID)) {

                $sql = "UPDATE Tasks SET Type = :type, Description = :description WHERE SubjectID = :subjectID AND Name = :name";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":type", $type);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":subjectID", $subjectID);
                $stmt->bindParam(":name", $name);
                $stmt->execute();

                generateHttpResponse(200, "Success", "Task Updated", "");
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