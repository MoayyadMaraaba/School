<?php
    try {
        $connectionString = "mysql:host=localhost:3308;dbname=test";
        $username = "root";
        $password = "";

        $pdo = new PDO($connectionString, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {
        die($ex->getMessage());
    }
?>