<?php

include_once "./classes/Database.php";
include_once "./includes/header.php";

$db = new Database();

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== "GET") {
    http_response_code(405);
    echo json_encode(
        [
            "status" => false,
            "message" => "method not allowed"
        ],
        JSON_PRETTY_PRINT
    );
    exit();
}

try {
    $url_path =  explode('/', $_SERVER['REQUEST_URI']);
    $id = end($url_path);

    if ($id === "") {
        http_response_code(400);
        echo json_encode(
            [
                "status" => false,
                "message" => "id required"
            ],
            JSON_PRETTY_PRINT
        );
        exit;
    }

    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode(
            [
                "status" => false,
                "message" => "id should be numeric value."
            ],
            JSON_PRETTY_PRINT
        );
        exit;
    }

    $sql = "SELECT * FROM employee WHERE id='$id'";
    $result = $db->conn->query($sql);
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        http_response_code(200);
        echo json_encode(
            [
                "status" => true,
                "message" => "data fetched successfully",
                $data
            ],
            JSON_PRETTY_PRINT
        );
    } else {
        http_response_code(404);
        echo json_encode(
            [
                "status" => false,
                "message" => "id no.{$id} not found",
            ],
            JSON_PRETTY_PRINT
        );
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(
        [
            "status" => false,
            "message" => "Error: " . $e->getMessage()
        ],
        JSON_PRETTY_PRINT
    );
}
