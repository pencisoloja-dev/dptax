<?php
// Al inicio del archivo, después de los headers
error_log("=== INICIANDO REQUEST ===");
error_log("METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("POST DATA: " . print_r($_POST, true));
error_log("INPUT: " . file_get_contents('php://input'));

// El resto del código sigue igual...
header("Access-Control-Allow-Origin: *");
// ... todo lo demás
