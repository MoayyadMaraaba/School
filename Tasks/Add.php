<?php

include "../config/keys.php";
include "../config/db.php";

require_once("../Utils/verifyToken.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get the authorization header and check if the token is valid
    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        generateHttpResponse(401, "Error", "Unauthorized", "");
        return;
    }

    $authHeader = $headers["Authorization"];
    $decoded = verifyToken($authHeader, TeacherSecret);

    if ($decoded != null) {
        // Get the data from the body
        if (isset($_POST["Name"]) && isset($_POST["Type"]) && isset($_POST["Description"]) && isset($_POST["SubjectID"])) {
            $name = $_POST["Name"];
            $type = $_POST["Type"];
            $description = $_POST["Description"];
            $subjectID = $_POST["SubjectID"];

            if (!empty($name) && !empty($type) && !empty($description) && !empty($subjectID)) {

                // check if name already exists
                $sql = "SELECT COUNT(*) FROM `tasks` WHERE tasks.SubjectID = :subjectID AND tasks.Name = :name";
                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':subjectID', $subjectID);
                $stmt->bindParam(':name', $name);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    generateHttpResponse(400, "Error", "Name already exists", "");
                    return;
                }

                $sql = "SELECT users.ID FROM subjects
                        LEFT JOIN classsubjects ON classsubjects.SubjectID = subjects.ID
                        LEFT JOIN classes ON classes.ID = classsubjects.ClassID
                        LEFT JOIN users ON users.ClassID = classes.ID
                        WHERE subjects.ID = :subjectID
                        GROUP BY users.ID";

                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(":subjectID", $subjectID);

                $stmt->execute();

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                for ($i = 0; $i < count($results); $i++) {
                    $sql = "INSERT INTO Tasks(Name, Type, Description, UserID, Mark, SubjectID) VALUES (:name, :type, :description, :userID, :mark, :subjectID)";
                    $stmt = $pdo->prepare($sql);

                    $mark = 0;

                    $stmt->bindParam(":name", $name);
                    $stmt->bindParam(":type", $type);
                    $stmt->bindParam(":description", $description);
                    $stmt->bindParam(":userID", $results[$i]["ID"]);
                    $stmt->bindParam(":mark", $mark);
                    $stmt->bindParam(":subjectID", $subjectID);

                    $stmt->execute();
                }


                generateHttpResponse(201, "Success", "Task Created", "");
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