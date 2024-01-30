<?php

include_once "./classes/Database.php";
include_once "./includes/header.php";

$db = new Database();

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== "GET") {
    http_response_code(405);
    echo json_encode(
        [
            "status" => true,
            "message" => "method not allowed"
        ]
    );
    exit();
}

try {
    $sql = "SELECT * FROM employee";
    $result = $db->conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            http_response_code(200);
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(
                [
                    "status" => true,
                    "message" => "All data fetched successfully",
                    "data" => $data
                ],
                JSON_PRETTY_PRINT
            );
        } else {
            http_response_code(404);
            echo json_encode(
                [
                    "status" => false,
                    "message" => "No data found"
                ],
                JSON_PRETTY_PRINT
            );
        }
    } else {
        throw new Exception("Query execution failed: " . $db->conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(
        [
            "status" => true,
            "message" => "Error: " . $e->getMessage()
        ],
        JSON_PRETTY_PRINT
    );
}
