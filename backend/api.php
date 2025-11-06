<?php
// DEBUG - TEMPORAL
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Manejar la solicitud OPTIONS (pre-flight) ---
// El navegador envía esto ANTES de la solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    //
    // --- ESTA ES LA CORRECCIÓN CLAVE ---
    // Volvemos a poner los headers de permiso, pero *dentro*
    // del bloque OPTIONS, para asegurar que se envíen con el 204.
    //
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
    
    // Respondemos con 204 (No Content)
    http_response_code(204); 
    exit; // Termina el script.
}

// --- De aquí en adelante, SÓLO se ejecuta para POST o GET ---

// --- Configuración de Headers para respuestas REALES (POST/GET) ---
// Nota: 'Allow-Origin' se pone de nuevo por si acaso es una solicitud 'simple'
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json"); // La respuesta real SÍ es JSON

// Para debugging - responder a GET requests
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo json_encode([
        'status' => 'online',
        'message' => 'DP Tax Backend is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// --- Cargar PHPMailer manually ---
require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// --- Leer variables de entorno ---
$smtp_host = getenv('SMTP_HOST');
$smtp_port = (int)getenv('SMTP_PORT');
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');
$hcaptcha_secret = getenv('HCAPTCHA_SECRET_KEY');
$mail_to = getenv('MAIL_TO');

// --- Función para devolver JSON ---
function send_json($success, $message, $debug = null) {
    $response = ['success' => $success, 'message' => $message];
    if ($debug !== null) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit;
}

// --- Validar que sea un POST ---
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    send_json(false, "Método no permitido.");
}

// --- Leer datos del formulario (soporta tanto FormData como JSON) ---
$input = $_POST;

// Si no hay datos en $_POST, intentar leer JSON
if (empty($input)) {
    $json_input = file_get_contents('php://input');
    $input = json_decode($json_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $input = [];
    }
}

// --- Recibir los datos del formulario ---
$name = isset($input['name']) ? htmlspecialchars($input['name']) : '';
$email = isset($input['email']) ? filter_var($input['email'], FILTER_SANITIZE_EMAIL) : '';
$phone = isset($input['phone']) ? htmlspecialchars($input['phone']) : '';
$message = isset($input['message']) ? htmlspecialchars($input['message']) : '';
$sms_consent = isset($input['sms_consent']) && $input['sms_consent'] ? 'Sí' : 'No';
$hcaptcha_response = isset($input['h-captcha-response']) ? $input['h-captcha-response'] : '';

// --- Validaciones básicas ---
if (empty($name) || empty($email) || empty($phone)) {
    send_json(false, "Por favor, completa todos los campos requeridos.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json(false, "Por favor, ingresa un email válido.");
}

// --- 1. Validar hCaptcha ---
if (empty($hcaptcha_response)) {
    send_json(false, "Por favor, completa la verificación hCaptcha.");
}

if (empty($hcaptcha_secret)) {
    send_json(false, "HCAPTCHA_SECRET_KEY no configurada en el servidor.");
}

// Validar con hCaptcha
$hcaptcha_url = "https://hcaptcha.com/siteverify";
$post_data = http_build_query([
    'secret' => $hcaptcha_secret,
    'response' => $hcaptcha_response
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $post_data
    ]
]);

$hcaptcha_verify = file_get_contents($hcaptcha_url, false, $context);
$hcaptcha_result = json_decode($hcaptcha_verify, true);

if (!$hcaptcha_result || !$hcaptcha_result['success']) {
    send_json(false, "Verificación hCaptcha fallida.");
}

// --- 2. Preparar y Enviar el Correo ---
$mail = new PHPMailer(true);

try {
    // Configuración del Servidor (Brevo)
    $mail->isSMTP();
    $mail->Host       = $smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtp_user;
    $mail->Password   = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtp_port;
    $mail->CharSet    = 'UTF-8';
    $mail->SMTPDebug = 0;

    // Destinatarios
    $mail->setFrom($smtp_user, 'Formulario Web DP Tax');
    $mail->addAddress($mail_to);
    if (!empty($email)) {
        $mail->addReplyTo($email, $name);
    }

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = "Nuevo Contacto de DP Tax: " . $name;
    $mail->Body    = "
        <h2>Nuevo Contacto desde DP Tax Preparation</h2>
        <p><strong>Nombre:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Teléfono:</strong> {$phone}</p>
        <p><strong>Mensaje:</strong></p>
        <p>" . nl2br($message) . "</p>
        <hr>
        <p><strong>Consentimiento SMS:</strong> {$sms_consent}</p>
        <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>
    ";

    $mail->AltBody = "Nuevo Contacto desde DP Tax Preparation\nNombre: {$name}\nEmail: {$email}\nTeléfono: {$phone}\nMensaje: {$message}\nConsentimiento SMS: {$sms_consent}\nFecha: " . date('Y-m-d H:i:s');

    $mail->send();
    send_json(true, "Mensaje enviado con éxito. Te contactaremos pronto."); 

} catch (Exception $e) {
    error_log("Error PHPMailer: " . $e->getMessage());
    send_json(false, "Error al enviar el mensaje. Por favor, intenta nuevamente.", ['smtp_error' => $mail->ErrorInfo]);
}
?>
