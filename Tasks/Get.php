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
$decoded = isAuthorized([StudentSecret, TeacherSecret, RegistrarSecret], $authHeader);

if ($decoded != null) {
    $sql = "SELECT * FROM Tasks";

    if ($decoded->role == "Student") {
        $sql .= " WHERE Tasks.UserID = :UserID";
    }

    $stmt = $pdo->prepare($sql);

    if ($decoded->role == "Student") {
        $stmt->bindParam(":UserID", $decoded->id);
    }
    
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    generateHttpResponse(200, "Success", "", ["Tasks" => $results]);
}
?>