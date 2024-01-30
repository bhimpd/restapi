<?php

include_once "./classes/Database.php";
include_once "./includes/header.php";

$db = new Database();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'PUT' && $method !== 'PATCH') {
    http_response_code(405);
    echo json_encode(
        [
            "status" => false,
            "message" => "Method not allowed"
        ],
        JSON_PRETTY_PRINT
    );
    exit;
}

try {
    $url_path =  explode('/', $_SERVER['REQUEST_URI']);
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

    $id_check_sql = "SELECT id FROM employee WHERE id = $id";
    $id_check_result = $db->conn->query($id_check_sql);
    if ($id_check_result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(
            [
                "status" => true,
                "message" => "ID not found"
            ],
            JSON_PRETTY_PRINT
        );
        exit;
    }


    $data = json_decode(file_get_contents("php://input"));

    $name = $data->name;
    $email = $data->email;
    $address = $data->address;
    $salary = $data->salary;

    $sql = "UPDATE student SET name='$name', email='$email', address='$address', age='$age' WHERE id=$id";
    $result = $db->conn->query($sql);

    if ($result) {
        http_response_code(200);
        $updated_data = [
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "address" => $address,
            "age" => $age
        ];
        echo json_encode(
            [
                "status" => true,
                "message" => "User updated successfully",
                "Updated Data" => $updated_data
            ],
            JSON_PRETTY_PRINT
        );
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
