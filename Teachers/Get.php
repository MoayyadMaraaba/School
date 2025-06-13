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
$decoded = verifyToken($authHeader, RegistrarSecret);

if ($decoded != null) {
    $sql = "SELECT * FROM users WHERE Role = 'Teacher'";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    generateHttpResponse(200, "Success", "", ["Teachers" => $results]);
}
?>
