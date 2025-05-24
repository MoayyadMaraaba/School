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
$decoded = verifyToken($authHeader, StudentSecret);

if ($decoded != null) {
    $sql = "SELECT ClassID FROM Users WHERE ID = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":id", $decoded->id);
    $stmt->execute();

    $classID = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["ClassID"];


    $sql = "SELECT Name,Day,Start,End FROM subjects LEFT JOIN classsubjects ON classsubjects.SubjectID = subjects.ID WHERE ClassID = :classID ORDER BY Day,Start ASC";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":classID", $classID);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    generateHttpResponse(200, "Success", "", ["Schedule" => $results]);
}

?>