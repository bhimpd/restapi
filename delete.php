<?php

include_once "./classes/Database.php";
include_once "./includes/header.php";

$db = new Database();

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== "DELETE") {
    http_response_code(405);
    echo json_encode(
        [
            "status" => true,
            "message" => "method not allowed"
        ],
        JSON_PRETTY_PRINT
    );
    exit();
}

$url_path = explode('/', $_SERVER['REQUEST_URI']);
$id = end($url_path);
if ($id === "") {
    http_response_code(400);
    echo json_encode(
        [
            "status" => true,
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
            "status" => true,
            "message" => "id should be numeric value."
        ],
        JSON_PRETTY_PRINT
    );
    exit;
}
try {
    $sql = "SELECT * FROM employee WHERE id = '$id'";
    $result = $db->conn->query($sql);
    $data = $result->fetch_assoc();

    $deletsql = "DELETE FROM employee WHERE id ='$id'";
    $deletedata = $db->conn->query($deletsql);
    if ($deletedata) {
        if ($db->conn->affected_rows > 0) {
            http_response_code(201);
            echo json_encode(
                [
                    "status" => true,
                    "message" => "Data deleted successfully",
                    "deleted data" => $data
                ],
                JSON_PRETTY_PRINT
            );
        } else {
            http_response_code(404);
            echo json_encode(
                [
                    "status" => true,
                    "message" => "ID not found"
                ],
                JSON_PRETTY_PRINT
            );
        }
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
