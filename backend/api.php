<?php
// Cargar el autoloader de Composer
require 'vendor/autoload.php';

// Usar las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Configuración de CORS y Headers ---
// Esto permite que tu index.html se comunique con esta API
header("Access-Control-Allow-Origin: *"); // En producción, deberías cambiar * por tu dominio
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejar la solicitud OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// --- Leer variables de entorno ---
// EasyPanel (y otros) cargan las variables de entorno automáticamente
$smtp_host = getenv('SMTP_HOST');
$smtp_port = (int)getenv('SMTP_PORT');
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');
$recaptcha_secret = getenv('RECAPTCHA_SECRET_KEY');
$mail_to = getenv('MAIL_TO');

// --- Función para devolver JSON y salir ---
function send_json($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
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
    send_json(false, "Fallo de reCAPTCHA.");
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
    ],
];
$context = stream_context_create($options);
$result = @file_get_contents($recaptcha_url, false, $context);
$recaptcha_result = json_decode($result, true);

if (!$result || !$recaptcha_result['success']) {
    send_json(false, "Verificación de reCAPTCHA fallida.");
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
    $mail->setFrom($smtp_user, 'Formulario Web DP Tax'); // El que envía (Brevo)
    $mail->addAddress($mail_to);                         // El que recibe (Tú)
    $mail->addReplyTo($email, $name);                    // Para poder "Responder" al cliente

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
    // Esta es la respuesta que tu index.html espera
    send_json(true, "Mensaje enviado con éxito."); 

} catch (Exception $e) {
    // Log de error (en un entorno real, esto iría a un archivo)
    // error_log("Error de PHPMailer: " . $mail->ErrorInfo);
    send_json(false, "Error al enviar el correo. Intente más tarde.");
}
?>