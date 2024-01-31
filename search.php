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
            "message" => "This method is not allowed."
        ],
        JSON_PRETTY_PRINT
    );
    exit;
}

if (isset($_GET['name'])) {
    $search_value = $_GET['name'];
    try {
        if (empty($search_value) || is_numeric($search_value)) {
            throw new Exception("Search value is either empty or not alphabets.");
        }

        $sql = "SELECT * FROM employee WHERE name LIKE '%$search_value%'";
        // Check if sort parameter is provided and valid
        if (isset($_GET['sort']) && ($_GET['sort'] === 'asc' || $_GET['sort'] === 'desc')) {
            $sort_order = strtoupper($_GET['sort']);
            $sql .= " ORDER BY name $sort_order"; // Assuming you want to sort by name
        }

        $result = $db->conn->query($sql);


        $total_rows = $result->num_rows;

        if ($total_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            http_response_code(200);
            echo json_encode(
                [
                    "status" => true,
                    "message" => "Data fetched successfully based on name search.",
                    "total data" => $total_rows,
                    "data" => $data
                ],
                JSON_PRETTY_PRINT
            );
        } else {
            http_response_code(404);
            echo json_encode(
                [
                    "status" => false,
                    "message" => "No data found based on name search."
                ],
                JSON_PRETTY_PRINT
            );
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(
            [
                "status" => false,
                "message" => "Error: " . $e->getMessage()
            ],
            JSON_PRETTY_PRINT
        );
    }
} elseif (isset($_GET['min'], $_GET['max']) && is_numeric($_GET['min']) && is_numeric($_GET['max'])) {
    $min_salary = $_GET['min'];
    $max_salary = $_GET['max'];

    $sql = "SELECT * FROM employee WHERE salary BETWEEN $min_salary AND $max_salary";

    if (isset($_GET['sort']) && ($_GET['sort'] === "asc" || ($_GET['sort'] === "desc"))) {
        $sort_order = strtoupper($_GET['sort']);
        $sql .= " ORDER BY salary $sort_order"; 
    }

    $result = $db->conn->query($sql);

    // var_dump($result);
    if ($result->num_rows > 0) {
        $total_rows= $result->num_rows;

        $data = $result->fetch_all(MYSQLI_ASSOC);
        http_response_code(200);
        echo json_encode(
            [
                "status" => true,
                "message" => "Data fetched successfully based on salary range.",
                "total data" => $total_rows,
                "data" => $data
            ],
            JSON_PRETTY_PRINT
        );
    } else {
        http_response_code(404);
        echo json_encode(
            [
                "status" => false,
                "message" => "No data found based on salary range."
            ],
            JSON_PRETTY_PRINT
        );
    }
} else {
    http_response_code(400);
    echo json_encode(
        [
            "status" => false,
            "message" => "Invalid parameters for search."
        ],
        JSON_PRETTY_PRINT
    );
}
