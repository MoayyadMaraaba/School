<?php

include "../config/keys.php";
include "../config/db.php";

require_once("../Utils/verifyToken.php");


// get the authorization header and check if the token is valid
$headers = getallheaders();

if (!isset($headers["Authorization"])) {
    generateHttpResponse(401, "Error", "Unauthorized", "");
    return;
}

$authHeader = $headers["Authorization"];
$decoded = isAuthorized([TeacherSecret, RegistrarSecret], $authHeader);

if ($decoded != null) {
    if (isset($_GET["classID"])) {
        $classID = $_GET["classID"];
        if (!empty($classID)) {
            $sql = "SELECT * FROM `subjects` LEFT JOIN classsubjects ON classsubjects.SubjectID = subjects.ID WHERE classsubjects.ClassID = :classID";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":classID", $classID);

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            generateHttpResponse(200, "Success", "Subjects in class", ["Subjects" => $results]);
            return;
        } else {
            // Empty inputs
            generateHttpResponse(400, "Error", "Please enter send correct parameters", "");
        }
    } else {
        // Missing inputs
        generateHttpResponse(400, "Error", "Wrong Data", "");
    }
}
?>