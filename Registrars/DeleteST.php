<?php
include "../config/db.php";

include "../config/keys.php";
include "../lib/php-jwt/JWT.php";
include "../lib/php-jwt/Key.php";

require_once("../Utils/verifyToken.php");
require_once("../Utils/helper.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get the authorization header and check if the token is valid
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        generateHttpResponse(401, "Error", "UnAuthorized", "");
        return;
    }

    $authHeader = $headers["Authorization"];
    $decoded = verifyToken($authHeader, RegistrarSecret);

    if ($decoded != null) {
        // check the request body
        if (isset($_POST["ID"])) {

            $id = $_POST["ID"];

            // check for empty strings
            if (!empty($id)) {
                $sql = "DELETE FROM Users WHERE ID = :id";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":id", $id);

                $stmt->execute();

                // Succesful response containing json web token
                generateHttpResponse(200, "Success", "User Deleted Successfully", "");
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