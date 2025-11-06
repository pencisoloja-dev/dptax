// ... existing code ...
$smtp_port = (int)getenv('SMTP_PORT');
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');
// $hcaptcha_secret = getenv('HCAPTCHA_SECRET_KEY'); // <-- Eliminado
$mail_to = getenv('MAIL_TO');

// --- Función para devolver JSON y salir ---
// ... existing code ...
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';
$sms_consent = isset($_POST['sms_consent']) ? 'Sí' : 'No';
// $hcaptcha_response = isset($_POST['h-captcha-response']) ? $_POST['h-captcha-response'] : ''; // <-- Eliminado

// --- 1. Validar hCaptcha --- (Todo este bloque ha sido eliminado)
// if (empty($hcaptcha_response)) {
//     send_json(false, "Por favor, completa la verificación hCaptcha.");
// }
//
// if (empty($hcaptcha_secret)) {
//     send_json(false, "HCAPTCHA_SECRET_KEY no configurada en el servidor.");
// }
//
// // Validar con hCaptcha
// $hcaptcha_verify = file_get_contents("https://hcaptcha.com/siteverify?secret=" . $hcaptcha_secret . "&response=" . $hcaptcha_response);
// $hcaptcha_result = json_decode($hcaptcha_verify, true);
//
// if (!$hcaptcha_result || !$hcaptcha_result['success']) {
//     send_json(false, "Verificación hCaptcha fallida.");
// }

// --- 1. Preparar y Enviar el Correo --- (Ahora es el paso 1)
$mail = new PHPMailer(true);

try {
// ... existing code ...
