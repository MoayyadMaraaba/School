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
$decoded = verifyToken($authHeader, TeacherSecret);

if ($decoded != null) {
    $sql = "SELECT Name,Day,Start,End FROM subjects WHERE TeacherID = :teacherID ORDER BY Day,Start ASC";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":teacherID", $decoded->id);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    generateHttpResponse(200, "Success", "", ["Schedule" => $results]);
}

?>