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
$decoded = isAuthorized([TeacherSecret], $authHeader);

if ($decoded != null) {
    $teacherID = $decoded->id;

    $sql = "SELECT classes.ID, classes.Name FROM classes LEFT JOIN classsubjects ON classes.ID = classsubjects.ClassID LEFT JOIN subjects ON classsubjects.SubjectID = subjects.ID WHERE subjects.TeacherID = :teacherID GROUP BY classes.ID";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":teacherID", $teacherID);

    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    generateHttpResponse(200, "Success", "Teacher Classes", ["Classes" => $results]);
    return;
}
?>