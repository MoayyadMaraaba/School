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
        if (isset($_POST["ID"]) && isset($_POST["Name"])) {
            $id = $_POST["ID"];
            $name = $_POST["Name"];

            if (!empty($id) && !empty($name)) {

                $sql = "UPDATE Classes SET Name = :name WHERE ID = :id";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":name", $name);

                if ($stmt->execute()) {
                    generateHttpResponse(200, "Success", "Class Updated", "");
                    return;
                } else {
                    generateHttpResponse(404, "Error", "Wrong ID", "");
                }
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