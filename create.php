<?php

include_once "./classes/Database.php";
include_once "./includes/header.php";

$db =  new Database();

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== "POST") {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "message" => "method not allowed"
    ]);
    exit;
}

try {
    $data =  json_decode(file_get_contents('php://input'));

    $name    = $data->name;
    $email   = $data->email;
    $address = $data->address;
    $salary  = $data->salary;

    if (empty($name) || empty($email) || empty($address) || empty($salary)) {
        throw new Exception("All fields are required.");
    }
    if (!is_numeric($salary)) {
        throw new Exception("Salary should be in number.");
    }
    $check_query = "SELECT COUNT(*) as count FROM employee WHERE email = '$email'";
    $check_result = $db->conn->query($check_query);
    $email_count = $check_result->fetch_assoc()['count'];
    if ($email_count > 0) {
        throw new Exception("Email already exists.");
    }

    $sql = "INSERT INTO employee (name,email,address,salary) VALUES ('$name','$email','$address','$salary')";
    $result = $db->conn->query($sql);
    if ($result) {
        http_response_code(201);
        $fetched_data = [
            "name" => $name,
            "email" => $email,
            "address" => $address,
            "salary" => $salary
        ];

        echo json_encode(
            [
                "status" => true,
                "message" => "user inserted successfully",
                "data" => $fetched_data
            ],
            JSON_PRETTY_PRINT
        );
    } else {
        http_response_code(500);
        echo json_encode(
            [
                "status" => false,
                "message" => "Error: " . $sql . "<br>" . $db->conn->error
            ],
            JSON_PRETTY_PRINT
        );
    }
} catch (\Exception $err) {
    http_response_code(400);
    echo json_encode(
        [
            "status" => false,
            "message" => $err->getMessage()
        ],
        JSON_PRETTY_PRINT
    );
}
