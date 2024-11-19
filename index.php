<?php
// index.php
header("Content-Type: application/json");

$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : null;

if ($endpoint) {
    $filePath = __DIR__ . "/api/{$endpoint}.php";

    if (file_exists($filePath)) {
        include $filePath;
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
    }
} else {
    echo json_encode(["message" => "Welcome to the API"]);
}