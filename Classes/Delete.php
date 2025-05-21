<?php

include "../config/keys.php";
include "../config/db.php";

require_once("../Utils/verifyToken.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get the authorization header and check if the token is valid
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        generateHttpResponse(401, "Error", "UnAuthorized", "");
        return;
    }

    $authHeader = $headers["Authorization"];
    $decoded = verifyToken($authHeader, RegistrarSecret);

    if ($decoded != null) {
        if (isset($_POST["ID"])) {
            $id = $_POST["ID"];

            if (!empty($id)) {
                $sql = "DELETE FROM Classes WHERE ID = :ID";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":ID", $id);

                $stmt->execute();

                generateHttpResponse(200, "Success", "Class Deleted", "");
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