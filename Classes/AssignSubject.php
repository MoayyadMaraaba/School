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
                $sql = "SELECT * FROM subjects WHERE ID = :id";
                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":id", $subjectID);
                $stmt->execute();

                $subject = $stmt->fetchAll(PDO::FETCH_ASSOC);


                $sql = "SELECT Start, End FROM `subjects` 
                        LEFT JOIN classsubjects ON classsubjects.SubjectID = subjects.ID 
                        WHERE classsubjects.ClassID = :classID AND (Start < :newEnd) AND (End > :newStart) AND Day = :day";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":classID", $classID);
                $stmt->bindParam(":newEnd", $subject[0]["End"]);
                $stmt->bindParam(":newStart", $subject[0]["Start"]);
                $stmt->bindParam(":day", $subject[0]["Day"]);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    generateHttpResponse(400, "Error", "Time is not available", "");
                    return;
                }

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