<?php
// Cargar el autoloader de Composer
require 'vendor/autoload.php';

// Usar las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Configuración de CORS y Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejar la solicitud OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// --- Leer variables de entorno ---
$smtp_host = getenv('SMTP_HOST');
$smtp_port = (int)getenv('SMTP_PORT');
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');
$recaptcha_secret = getenv('RECAPTCHA_SECRET_KEY');
$mail_to = getenv('MAIL_TO');

// --- Función para devolver JSON y salir ---
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

// --- Recibir los datos del formulario ---
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';
$sms_consent = isset($_POST['sms_consent']) ? 'Sí' : 'No';
$recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

// --- 1. Validar reCAPTCHA ---
if (empty($recaptcha_response)) {
    send_json(false, "Token de reCAPTCHA no recibido.");
}

if (empty($recaptcha_secret)) {
    send_json(false, "RECAPTCHA_SECRET_KEY no configurada en el servidor.", ['env_missing' => true]);
}

$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
$recaptcha_data = [
    'secret'   => $recaptcha_secret,
    'response' => $recaptcha_response,
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($recaptcha_data),
        'timeout' => 10
    ],
];
$context = stream_context_create($options);
$result = @file_get_contents($recaptcha_url, false, $context);

if ($result === false) {
    send_json(false, "No se pudo conectar con Google reCAPTCHA.", ['connection_error' => true]);
}

$recaptcha_result = json_decode($result, true);

if (!$recaptcha_result || !isset($recaptcha_result['success'])) {
    send_json(false, "Respuesta inválida de reCAPTCHA.", ['invalid_response' => true, 'raw' => $result]);
}

if (!$recaptcha_result['success']) {
    $error_codes = isset($recaptcha_result['error-codes']) ? $recaptcha_result['error-codes'] : ['unknown'];
    send_json(false, "Verificación de reCAPTCHA fallida.", ['error_codes' => $error_codes]);
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

    // Destinatarios
    $mail->setFrom($smtp_user, 'Formulario Web DP Tax');
    $mail->addAddress($mail_to);
    $mail->addReplyTo($email, $name);

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
    ";

    $mail->send();
    send_json(true, "Mensaje enviado con éxito."); 

} catch (Exception $e) {
    send_json(false, "Error al enviar el correo.", ['smtp_error' => $mail->ErrorInfo]);
}
?>
