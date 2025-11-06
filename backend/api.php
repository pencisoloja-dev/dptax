<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// DEBUG: Mostrar qué está recibiendo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = $_POST;
    $inputData = file_get_contents('php://input');
    
    echo json_encode([
        'debug' => true,
        'post_data' => $postData,
        'raw_input' => $inputData,
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'none',
        'message' => 'Datos recibidos para debugging'
    ]);
    exit;
}

// El resto del código normal para GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo json_encode([
        'status' => 'online', 
        'message' => 'DP Tax Backend is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}
