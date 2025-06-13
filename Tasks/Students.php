<?php
include "../config/keys.php";
include "../config/db.php";

require_once("../Utils/verifyToken.php");

// get the authorization header and check if the token is valid
$headers = getallheaders();

if (!isset($headers["Authorization"])) {
    generateHttpResponse(401, "Error", "UnAuthorized", "");
    return;
}

$authHeader = $headers["Authorization"];
$decoded = isAuthorized([TeacherSecret], $authHeader);

if ($decoded != null) {

    if (isset($_GET["taskName"]) && isset($_GET["subjectID"])) {
        $taskName = $_GET["taskName"];
        $subjectID = $_GET["subjectID"];

        if (!empty($taskName) && !empty($subjectID)) {

            $sql = "SELECT tasks.UserID AS UserID,tasks.Name AS taskName,tasks.SubjectID, users.Name, users.Email, tasks.Answer, tasks.Mark FROM tasks 
            LEFT JOIN users ON users.ID = tasks.UserID 
            WHERE tasks.Name = :taskName and SubjectID = :subjectID";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":taskName", $taskName);
            $stmt->bindParam(":subjectID", $subjectID);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            generateHttpResponse(200, "Success", "", ["Tasks" => $results]);
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